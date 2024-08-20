<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerPackage;
use Crater\Models\Invoice;
use Crater\Models\User;
use Crater\Traits\ServicesRecalculateTrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Vinkla\Hashids\Facades\Hashids;

class CheckServiceRenewalDate extends Command
{
    use ServicesRecalculateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:service:renewal_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check renewal date of service to updated and create invoices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        \Log::debug("-------------------------------------------------------");
        \Log::debug("Inicio del proceso de facturación de servicios normales");

        $company_ids_with_active_job = CompanySetting::where('option', 'allow_renewal_date_job')
            ->where('value', 1)
            ->pluck('company_id');
        \Log::debug("Company IDs con trabajo activo:", $company_ids_with_active_job->toArray());

        //$service = CustomerPackage::where('id',32)->first();

        // $this->calculatePriceService($service, true);

        if (count($company_ids_with_active_job) != 0) {
            \Log::debug("Entrando al primer if: existen company_ids con trabajo activo");

            $time = Carbon::now()->format('H:i');
            \Log::debug("Hora actual: $time");

            $company_ids_match_time = CompanySetting::whereIn('company_id', $company_ids_with_active_job)
                ->where('option', 'time_run_renewal_date_job')
                ->where('value', $time)
                ->pluck('company_id');
            \Log::debug("Company IDs con coincidencia de tiempo:", $company_ids_match_time->toArray());

            if (count($company_ids_match_time) != 0) {
                \Log::debug("Entrando al segundo if: existen company_ids con coincidencia de tiempo");

                $companies = Company::whereIn('id', $company_ids_match_time)->get();
                \Log::debug("Empresas con tiempo de ejecución coincidente:", $companies->toArray());

                foreach ($companies as $company) {
                    \Log::debug("Entrando al primer foreach: procesando la empresa con id: {$company->id}");

                    $services = CustomerPackage::where('company_id', $company->id)
                        ->whereIn('status', ['A', 'S'])
                        ->whereBetween('renewal_date', [Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(31)->format('Y-m-d')])
                        ->with('items', 'items.taxes', 'taxes')
                        ->get();
                    \Log::debug("Servicios para la empresa {$company->id}:", $services->toArray());

                    // Iterar sobre $services si no está vacío
                    if (! $services->isEmpty()) {
                        \Log::debug("IDs de servicios para la empresa {$company->id}:");
                        foreach ($services as $service) {
                            \Log::debug("ID: {$service->id}, Código: {$service->code}");
                        }
                    } else {
                        \Log::debug("No hay servicios para la empresa {$company->id}.");
                    }

                    foreach ($services as $service) {
                        \Log::debug("\\\Entrando al segundo foreach: procesando el servicio con id: {$service->id}");

                        $now = Carbon::now()->format('Y-m-d');
                        $customer = User::with('customerConfigs')->find($service->customer_id);
                        \Log::debug("Cliente del servicio {$service->id}:");
                        \Log::debug($customer);

                        if ($customer) {
                            \Log::debug("Cliente encontrado para el servicio {$service->id}");

                            if ($customer->customerConfigs) {
                                \Log::debug("Configuraciones del cliente disponibles para el servicio {$service->id}");

                                $date_to_invoice = Carbon::parse($service->renewal_date)
                                    ->subDays($customer->customerConfigs->invoice_days_before_renewal)
                                    ->format('Y-m-d');
                                \Log::debug("Fecha para facturar el servicio {$service->id}: $date_to_invoice");

                                if ($customer->customerConfigs->invoice_suspended_services) {
                                    \Log::debug("Generando facturas para servicios suspendidos del cliente {$customer->id}");
                                    $this->generateInvoices($service, $customer, $date_to_invoice, $now);
                                } else {
                                    if (($service->status == 'A')) {
                                        \Log::debug("Generando facturas para servicios activos del cliente {$customer->id}");
                                        $this->generateInvoices($service, $customer, $date_to_invoice, $now);
                                    }
                                }
                            } else {
                                \Log::debug("No hay configuraciones del cliente para el servicio {$service->id}");

                                $date_to_invoice = Carbon::parse($service->renewal_date)
                                    ->subDays(0)
                                    ->format('Y-m-d');
                                \Log::debug("Fecha para facturar el servicio {$service->id}: $date_to_invoice");

                                if (($service->status == 'A') && (strcmp($date_to_invoice, $now) == 0)) {
                                    \Log::debug("Creando factura para el servicio {$service->id}");
                                    self::createInvoice($service);
                                }
                            }
                        } else {
                            \Log::debug("No se encontró cliente para el servicio {$service->id}");
                        }
                    }

                    $services_to_renew = CustomerPackage::where('company_id', $company->id)
                        ->whereIn('status', ['A', 'S'])
                        ->whereBetween('renewal_date', [Carbon::now()->subDays(1000)->format('Y-m-d'), Carbon::now()->format('Y-m-d')])
                        ->with('items', 'items.taxes', 'taxes')
                        ->get();
                    \Log::debug("---Servicios a renovar para la empresa anteriores {$company->id}:", $services_to_renew->toArray());

                    // Iterar sobre $services_to_renew si no está vacío
                    if (! $services_to_renew->isEmpty()) {
                        \Log::debug("IDs de servicios a renovar para la empresa {$company->id}:");
                        foreach ($services_to_renew as $service) {
                            \Log::debug("ID: {$service->id}, Código: {$service->code}");
                        }
                    } else {
                        \Log::debug("No hay servicios a renovar para la empresa anteriores {$company->id}.");
                    }

                    foreach ($services_to_renew as $service) {
                        \Log::debug("-\-\-Entrando al tercer foreach: procesando el servicio a renovar con id: {$service->code}");

                        $customer = User::with('customerConfigs')->find($service->customer_id);
                        \Log::debug("Cliente del servicio a renovar {$service->id}:");
                        \Log::debug($customer);

                        if ($customer) {
                            \Log::debug("Cliente encontrado para el servicio a renovar {$service->id}");

                            if ($customer->customerConfigs) {
                                \Log::debug("Configuraciones del cliente disponibles para el servicio a renovar {$service->code}");

                                if ($customer->customerConfigs->invoice_suspended_services || $service->status == 'A') {
                                    \Log::debug("Verificando si se debe crear factura para el servicio a renovar {$service->code}");

                                    $invcont = Invoice::where("user_id", $service->customer_id)
                                        ->where("customer_packages_id", $service->id)
                                        ->where("pbxservice_date_prev", $service->date_prev)
                                        ->where("pbxservice_date_renewal", Carbon::parse($service->renewal_date)->format('Y-m-d'))
                                        ->get()
                                        ->count();
                                    \Log::debug("Conteo de facturas existentes para el servicio a renovar {$service->code}: $invcont");

                                    if ($invcont == 0) {
                                        \Log::debug("Creando factura para el servicio a renovar {$service->id}");
                                        self::createInvoice($service);
                                    }
                                    $service->date_prev = $service->renewal_date;
                                    $current_renewal_date = Carbon::parse($service->renewal_date);
                                    $service->renewal_date = $this->getRenewalDate($service, $current_renewal_date);
                                    $service->save();
                                }
                            } else {
                                \Log::debug("No hay configuraciones del cliente para el servicio a renovar {$service->code}");

                                if ($service->status == 'A') {
                                    \Log::debug("Verificando si se debe crear factura para el servicio activo a renovar {$service->code}");

                                    $invcont = Invoice::where("user_id", $service->customer_id)
                                        ->where("customer_packages_id", $service->id)
                                        ->where("pbxservice_date_prev", $service->date_prev)
                                        ->where("pbxservice_date_renewal", Carbon::parse($service->renewal_date)->format('Y-m-d'))
                                        ->get()
                                        ->count();
                                    \Log::debug("Conteo de facturas existentes para el servicio activo a renovar {$service->code}: $invcont");

                                    if ($invcont == 0) {
                                        \Log::debug("Creando factura para el servicio activo a renovar {$service->code}");
                                        self::createInvoice($service);
                                    }
                                    $service->date_prev = $service->renewal_date;
                                    $current_renewal_date = Carbon::parse($service->renewal_date);
                                    $service->renewal_date = $this->getRenewalDate($service, $current_renewal_date);
                                    $service->save();
                                }
                            }
                        } else {
                            \Log::debug("No se encontró cliente para el servicio a renovar {$service->code}");
                        }
                    }

                    self::pendingServiceActivation($company);
                    \Log::debug("Activación de servicios pendientes para la empresa {$company->id}");
                }
            } else {
                \Log::debug("No hay company_ids con coincidencia de tiempo");
            }
        } else {
            \Log::debug("No hay company_ids con trabajo activo");
        }

        \Log::debug("Fin del proceso de facturación de servicios normales");
        \Log::debug("-------------------------------------------------------");
    }

    public function generateInvoices($service, $customer, $date_to_invoice, $now)
    {
        \Log::debug('Inicio del método generateInvoices.');
        \Log::debug("Parámetros recibidos - service: {$service->term}, customer: {$customer->id}, date_to_invoice: {$date_to_invoice}, now: {$now}");

        switch ($service->term) {
            case 'daily':
                \Log::debug('Entró al case daily.');
                $date_to_invoice_daily = Carbon::parse($service->renewal_date)
                    ->format('Y-m-d');
                if (strcmp($date_to_invoice_daily, $now) == 0) {
                    \Log::debug('La fecha de facturación diaria coincide con now, se crea la factura.');
                    $invoice = self::createInvoice($service);
                } else {
                    \Log::debug('La fecha de facturación diaria no coincide con now, no se crea la factura.');
                }

                break;
            case 'weekly':
                \Log::debug('Entró al case weekly.');
                if ($customer->customerConfigs->invoice_days_before_renewal >= 6) {
                    $date_to_invoice_weekly = Carbon::parse($service->renewal_date)
                        ->subDays(6)
                        ->format('Y-m-d');
                    if (strcmp($date_to_invoice_weekly, $now) == 0) {
                        \Log::debug('La fecha de facturación semanal coincide con now, se crea la factura.');
                        $invoice = self::createInvoice($service);
                    } else {
                        \Log::debug('La fecha de facturación semanal no coincide con now, no se crea la factura.');
                    }
                } else {
                    \Log::debug('Entró al else del case weekly.');
                    if (strcmp($date_to_invoice, $now) == 0) {
                        \Log::debug('La fecha de facturación coincide con now en el else del case weekly, se crea la factura.');
                        $invoice = self::createInvoice($service);
                    } else {
                        \Log::debug('La fecha de facturación no coincide con now en el else del case weekly, no se crea la factura.');
                    }
                }

                break;
            default:
                \Log::debug('Entró al case default para otros términos de facturación.');
                if (strcmp($date_to_invoice, $now) == 0) {
                    \Log::debug('La fecha de facturación coincide con now en el case default, se crea la factura.');
                    $invoice = self::createInvoice($service);
                } else {
                    \Log::debug('La fecha de facturación no coincide con now en el case default, no se crea la factura.');
                }

                break;
        }

        \Log::debug('Fin del método generateInvoices.');
    }

    public static function getRenewalDate($service, $current_renewal_date)
    {
        $renewal_date = '';
        switch ($service->term) {
            case CustomerPackage::TERM_DAILY:
                $renewal_date = $current_renewal_date->addDay();

                break;
            case CustomerPackage::TERM_WEEKLY:
                $renewal_date = $current_renewal_date->addWeek();

                break;
            case CustomerPackage::TERM_MONTHLY:
                $renewal_date = $current_renewal_date->addMonth();

                break;
            case CustomerPackage::TERM_BIMONTHLY:
                $renewal_date = $current_renewal_date->addMonths(2);

                break;
            case CustomerPackage::TERM_QUARTERLY:
                $renewal_date = $current_renewal_date->addQuarter();

                break;
            case CustomerPackage::TERM_BIANNUAL:
                $renewal_date = $current_renewal_date->addMonths(6);

                break;
            case CustomerPackage::TERM_YEARLY:
                $renewal_date = $current_renewal_date->addYear();

                break;
            case CustomerPackage::TERM_ONE_TIME:
                $renewal_date = Carbon::now();

                break;
        }

        return $renewal_date->format('Y-m-d');
    }

    public static function createInvoice($service)
    {

        $exist = Invoice::where('customer_packages_id', $service->id)->where('pbxservice_date_prev', $service->date_prev)
            ->where('pbxservice_date_renewal', $service->renewal_date)->first();

        if ($exist != null) {
            \Log::debug("Ya existe factura para servicio: ".$service->code);

            return false;
        }

        ///se busca el numeral del invoice
        $prefix = CompanySetting::getSetting('invoice_prefix', $service->company_id);
        $nextNumber = Invoice::getNextInvoiceNumber($prefix);

        // funcion que valida si el descuento sigue siendo valido, sino los suspende
        //validar que solo se ejecute cuando allow_discount == 1
        self::checkDiscount($service);

        // Obtener el valor de 'invoice_issuance_period' para la compañía específica
        $invoiceIssuancePeriod = CompanySetting::getSetting('invoice_issuance_period', $service->company_id);

        // Validar si el valor obtenido es nulo, vacío o no definido
        // y asignar 7 a DueDateDays si es así
        if (empty($invoiceIssuancePeriod)) {
            $DueDateDays = 7;
        } elseif (is_numeric($invoiceIssuancePeriod)) {
            // Si es un string numérico, convertir a entero
            if (is_string($invoiceIssuancePeriod) && ctype_digit($invoiceIssuancePeriod)) {
                $DueDateDays = (int) $invoiceIssuancePeriod;
            } elseif (is_int($invoiceIssuancePeriod)) {
                // Si ya es un entero, asignarlo directamente
                $DueDateDays = $invoiceIssuancePeriod;
            } else {
                // Si no es un string numérico ni un entero, asignar 7
                $DueDateDays = 7;
            }
        } else {
            // Si no es numérico, asignar 7
            $DueDateDays = 7;
        }

        ///pakage name

        $packagename = "N/A";

        if ($service->package !== null) {
            $packagename = $service->package->name;
        }


        ///cambios de fechas

        $current_renewal_date = Carbon::parse($service->renewal_date);
        $next_renewal_date = self::getRenewalDate($service, $current_renewal_date);

        ///se genera o se crea el invoice en general, el cuerpo
        //la info que se guarda en la tabla invoice
        $invoice = Invoice::create([
            'invoice_date' => Carbon::now()->format('Y-m-d'),
            'due_date' => Carbon::now()->addDays($DueDateDays)->format('Y-m-d'),
            'invoice_number' => $prefix.'-'.$nextNumber,
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'tax_per_item' => $service->tax_by == 'I' ? 'YES' : 'NO',
            'discount_per_item' => $service->discount_by == 'I' ? 'YES' : 'NO',
            'notes' => 'Invoice created from services '.$service->code.' - '.$packagename."     Billing Period: ".$service->date_prev." - ".Carbon::parse($service->renewal_date)->format('Y-m-d'),
            'discount_type' => $service->discount_type,
            'discount' => $service->discount,
            'discount_val' => $service->discount_val,
            'sub_total' => $service->sub_total,
            'total' => $service->total,
            'tax' => $service->tax,
            'invoice_template_id' => 1,
            'due_amount' => $service->total,
            'pbxservice_date_prev' => $service->date_prev,
            'pbxservice_date_renewal' => Carbon::parse($service->renewal_date)->format('Y-m-d'),
            'end_period_services' => Carbon::parse($next_renewal_date)->format('Y-m-d'),
            'user_id' => $service->customer_id,
            'company_id' => $service->company_id,
            'creator_id' => $service->creator_id,
            'addresses_id' => $service->addresses_id,
        ]);
        // se agregan la fecha anterio de renovacio, la fecha nueva de renovacio y el codigo del servicio
        $invoice->pbxservice_date_prev = $service->date_prev;
        $invoice->customer_packages_id = $service->id;
        $invoice->pbxservice_date_renewal = Carbon::parse($service->renewal_date)->format('Y-m-d');

        $invoice->unique_hash = Hashids::connection(Invoice::class)->encode($invoice->id * rand(2, 105)); // metodo
        $invoice->save();

        /* while (Invoice::where('unique_hash', $invoice->unique_hash)->count() != 1){
        $invoice->unique_hash = General::generateUniqueId();
        $invoice->save();
        }*/

        // en este metodo seguardan los items
        //tener cuidado es que los nombres de los campos coincidan,
        //los valores se guarden correctamente
        //que los taxes per item se rguarden tambien, cuando tax type == I
        self::createItems($invoice, $service);

        // aqui se guardan los taxes en general
        foreach ($service->taxes as $tax) {
            // $invoice->taxes()->create($tax->toArray());
            $data = [
                'tax_type_id' => $tax['tax_type_id'],
                'invoice_id' => $invoice->id,
                'estimate_id' => null,
                'invoice_item_id' => null,
                'item_id' => null,
                'company_id' => $tax['company_id'],
                'name' => $tax['name'],
                'amount' => $tax['amount'],
                'percent' => $tax['percent'],
                'compound_tax' => $tax['compound_tax'],
                'pbx_package_id' => null,
                'pbx_package_item_id' => null,
                'pbx_service_item_id' => null,
                'package_id' => $tax['customer_package_id'],
                'package_item_id' => $tax['customer_package_item_id'],
            ];
            DB::table('taxes')->insert($data);
        }

        ServicesRecalculateTrait::calculatePriceService($service, true);
    }

    public static function createItems($invoice, $service)
    {
        foreach ($service->items as $item) {
            $invoice_item = $invoice->items()->create($item->toArray());

            //validar cuando el servicio sea tax type  == general
            //no se vaya a guardar los taxes per item.
            foreach ($item->taxes as $tax) {
                $invoice_item->taxes()->create($tax->toArray());
            }
        }
    }

    public static function pendingServiceActivation($company)
    {
        $pending_services = CustomerPackage::where('company_id', $company->id)
            ->where('status', 'P')
            ->whereDate('activation_date', "<=", Carbon::now())
            ->get();

        foreach ($pending_services as $service) {
            $service->status = 'A';
            $service->save();
        }
    }

    public static function checkDiscount($service)
    {
        $discount = $service->discounts->first();
        $discount_deadline = '';

        if ($discount != null) {

            // validacion cuando la fecha es por unidade tiempo,  a la fecha de activacion se le suma
            //las unidades de tiempo, y se genera una fecha final
            //Log::debug("discount");
            //Log::debug($discount);
            if ($discount->term_type == 'U') {
                $initial_date = Carbon::parse($service->activation_date);

                switch ($discount->term) {
                    case 'days':
                        $discount_deadline = $initial_date->addDays($discount->time_unit_number);

                        break;

                    case 'weeks':
                        $discount_deadline = $initial_date->addWeeks($discount->time_unit_number);

                        break;

                    case 'months':
                        $discount_deadline = $initial_date->addMonths($discount->time_unit_number);

                        break;

                    case 'years':
                        $discount_deadline = $initial_date->addYears($discount->time_unit_number);

                        break;
                }

            } else {
                //cuando el descuento esntre fechas, se parsea la fecha final.
                $discount_deadline = Carbon::parse($discount->end_date);
            }

            //Log::debug($discount_deadline);
            //Log::debug($discount_deadline->lte(Carbon::now()));
            //Log::debug($discount_deadline->gte(Carbon::now()));
            // si la decha final coincide con la fecha actual se desactiva el descuento
            if ($discount_deadline->lte(Carbon::now())) {
                $service->allow_discount = 0;
                $service->save();
            }

        }
    }
}
