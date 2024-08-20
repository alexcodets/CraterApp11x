<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Jobs\PbxGenerateAvalaraTaxesJob;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\InvoiceAppRates;
use Crater\Models\Item;
use Crater\Models\PbxAdditionalCharge;
use Crater\Models\PbxDID;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesAppRate;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxServicesTaxTypesCdr;
use Crater\Models\ProfileDID;
use Crater\Models\ProfileDidTollFree;
use Crater\Models\ProfileExtensions;

//
use Crater\Models\User;
use Crater\Traits\PbxServicesReCalculateTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Log;
use Throwable;
use Vinkla\Hashids\Facades\Hashids;

class CheckPbxServiceRenewalDate extends Command
{
    use PbxServicesReCalculateTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pbx-service:renewal_date';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check renewal date of pbx service to updated and create invoices';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): int
    {

        Log::debug('Inicio del metodo checkpbxservie');
        // Se obtienen los IDs de las compañías con trabajo activo
        $company_ids_with_active_job = CompanySetting::where('option', 'allow_renewal_date_job_pbx')
            ->where('value', 1)
            ->pluck('company_id');
        Log::debug('IDs de compañías con trabajo activo: ', $company_ids_with_active_job->toArray());

        //  $service = PbxServices::where('id', 11)->first();
        //PbxServicesReCalculateTrait::calculatePriceService($service, true);

        if (count($company_ids_with_active_job) != 0) {
            $time = now()->format('H:i');
            Log::debug('Hora actual: '.$time);

            // Se obtienen los IDs de las compañías que coinciden con el tiempo de ejecución
            $company_ids_match_time = CompanySetting::whereIn('company_id', $company_ids_with_active_job)
                ->where('option', 'time_run_renewal_date_job_pbx')
                ->where('value', $time)
                ->pluck('company_id');
            Log::debug('IDs de compañías que coinciden con el tiempo de ejecución: ');
            Log::debug($company_ids_match_time);
            if (count($company_ids_match_time) != 0) {
                $companies = Company::whereIn('id', $company_ids_match_time)->get();

                foreach ($companies as $company) {
                    // se traen todos los servicios de los siguientes 30 dias para crear la facturacion
                    Log::debug('Compañía: '.$company->name);

                    Log::debug('^*procesar servicios a futuro*');
                    $enableInvoicesServices = PbxServices::where('company_id', $company->id)
                        ->where('status', '!=', 'C')
                        ->where('status', '!=', 'P')
                        ->whereBetween('renewal_date', [Carbon::now()->format('Y-m-d'), Carbon::now()->addDays(31)->format('Y-m-d')])
                        ->get();
                    Log::debug('Servicios habilitados para facturación  enableInvoicesServices: '.$enableInvoicesServices->count());
                    foreach ($enableInvoicesServices as $serviceInvoice) {
                        Log::debug('Servicio: '.$serviceInvoice->pbx_services_number);
                    }
                    foreach ($enableInvoicesServices as $serviceInvoice) {
                        Log::debug('***Servicio a procesar: '.$serviceInvoice->pbx_services_number);
                        $now = Carbon::now()->format('Y-m-d');
                        $customer = User::with('customerConfigs')->find($serviceInvoice->customer_id);

                        // Se verifica si el cliente y su configuración existen
                        if ($customer) {
                            Log::debug('Cliente encontrado: '.$customer->id);
                            if ($customer->customerConfigs) {
                                // Se calcula la fecha de factura

                                Log::debug('Cliente dias antes de renovacion: '.$customer->customerConfigs->invoice_days_before_renewal);
                                $date_to_invoice = Carbon::parse($serviceInvoice->renewal_date)
                                    ->subDays(intval($customer->customerConfigs->invoice_days_before_renewal))
                                    ->format('Y-m-d');

                                Log::debug('fecha a buscs: '.$date_to_invoice);
                                Log::debug('fecha a actual: '.$now);
                                Log::debug('fecha resultados');
                                Log::debug(strcmp($date_to_invoice, $now));

                                // Si está activa la facturación para servicios suspendidos
                                if ($customer->customerConfigs->invoice_suspended_services) {
                                    // Se facturan ambos tipos de servicios
                                    Log::debug('Facturación activa para servicios suspendidos');

                                    $this->generateInvoices($serviceInvoice, $customer, $date_to_invoice, $now);

                                } else {
                                    // Solo se facturan servicios activos
                                    if (($serviceInvoice->status == 'A')) {
                                        Log::debug('Facturación solo para servicios activos');
                                        $this->generateInvoices($serviceInvoice, $customer, $date_to_invoice, $now);
                                    }
                                }
                            } else {
                                // Si el cliente no tiene configuración, se factura en el tiempo determinado en el servicio
                                Log::debug('Cliente sin configuración');
                                $date_to_invoice = Carbon::parse($serviceInvoice->renewal_date)
                                    ->subDays(0)
                                    ->format('Y-m-d');
                                if (($serviceInvoice->status == 'A') && (strcmp($date_to_invoice, $now) == 0)) {
                                    Log::debug('---Generando factura para clientes customerconfigs---');
                                    $invoice = $this->createInvoice($serviceInvoice);

                                    try {
                                        if ($invoice != null) {
                                            PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                                        }
                                    } catch (Exception $e) {
                                        Log::debug($e->getMessage());
                                    }
                                }
                            }
                        } else {
                            // Si el cliente no existe, se factura en el tiempo determinado en el servicio
                            Log::debug('Cliente no encontrado');
                            $date_to_invoice = Carbon::parse($serviceInvoice->renewal_date)
                                ->subDays(0)
                                ->format('Y-m-d');
                            if (($serviceInvoice->status == 'A') && (strcmp($date_to_invoice, $now) == 0)) {
                                Log::debug('---Generando factura sin cliente---');
                                $invoice = $this->createInvoice($serviceInvoice);

                                try {
                                    if ($invoice != null) {
                                        PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                                    }
                                } catch (Exception $e) {
                                    Log::debug($e->getMessage());
                                }
                            }
                        }
                    }

                    Log::debug('^*procesar servicios al dia*');
                    // Se buscan todos los servicios para realizar la renovación de cada servicio
                    $services = PbxServices::where('company_id', $company->id)
                        ->where('status', '!=', 'C')
                        ->where('status', '!=', 'P')
                        ->whereDate('renewal_date', '<=', now())
                        ->get();

                    Log::debug('***Servicios habilitados para facturación: anteriormente: '.$services->count());

                    foreach ($services as $serviceInvoice) {
                        Log::debug('Servicio ant: '.$serviceInvoice->pbx_services_number);
                    }

                    foreach ($services as $service) {
                        $customer = User::with('customerConfigs')->find($service->customer_id);

                        if ($customer) {
                            // Si está activa la facturación para servicios suspendidos se actualiza la renovación de ambos tipos de servicio, sino solo se hará para servicios activos
                            Log::debug('Compañía: '.$company->name.' - Servicio: '.$service->id);
                            $try = false;
                            if ($customer->customerConfigs != null) {
                                $try = $customer->customerConfigs->invoice_suspended_services;
                            }
                            if ($try || $service->status == 'A') {
                                // Entrando al primer if: Facturación activa para servicios suspendidos o servicio está activo
                                //se crea o valida la factura primero

                                Log::debug('Create invoice customer en servicios habilitados: ');
                                $invoice = $this->createInvoice($service);

                                // se actualiza después
                                Log::debug('Fecha de renovación actual: '.$service->renewal_date);
                                $current_renewal_date = Carbon::parse($service->renewal_date);

                                Log::debug('Minutos inclusivos: '.($service->pbxPackage ? $service->pbxPackage->inclusive_minutes : 'null'));
                                $inclusive_minutes = $service->pbxPackage ? $service->pbxPackage->inclusive_minutes : null;

                                Log::debug('Fecha de renovación anterior: '.$service->date_prev);
                                $service->date_prev = $service->renewal_date;

                                Log::debug('Calculando nueva fecha de renovación.');
                                $service->renewal_date = $this->getRenewalDate($service, $current_renewal_date);
                                Log::debug('Nueva fecha de renovación: '.$service->renewal_date);

                                if ($service->allow_pbx_packages_update) {
                                    // Se actualiza cap_extension
                                    $service->cap_extension = $service->pbxPackage ? $service->pbxPackage->inclusive_minutes : $service->cap_extension;
                                    $total = PbxServicesExtensions::where('pbx_service_id', $service->id)->whereNULL('deleted_at')->get()->count();
                                    $service->cap_total = $total * $service->cap_extension;
                                    // actualizar extensions
                                }
                                if ($inclusive_minutes) {
                                    // tener en cuenta para el update de pbx packages
                                    $service->inclusive_minutes_seconds_consumed = $service->cap_total * 60;
                                }
                                $service->save();
                                Log::debug('Servicio actualizado: '.$service->id);

                                // Validar descuentos, desactivar si cumplen las reglas
                                if ($service->allow_discount) {
                                    // D - dates
                                    // T  - period
                                    if ($service->discount_term_type == 'D') {
                                        if (! is_null($service->date_to) && ! is_null($service->date_from)) {
                                            $date = Carbon::now()->format('Y-m-d');
                                            if ($service->date_to < $date) {
                                                // desactivar descuentos
                                                $service->allow_discount = 0;
                                                $service->save();
                                                Log::debug('Descuento desactivado para el servicio: '.$service->id);
                                            } else {

                                                if ($service->date_to == $date) {
                                                    PbxServicesReCalculateTrait::calculatePriceService($service);
                                                }

                                            }
                                        }
                                    }

                                    if ($service->discount_term_type == 'T') {
                                        // Days - Weeks - Months - Years
                                        $date = '';
                                        switch ($service->time_period_value) {
                                            case 'Days':
                                                $result = Carbon::create($service->date_begin)->format('Y-m-d');
                                                $date = Carbon::parse($result)->addDays($service->time_period);

                                                break;

                                            case 'Weeks':
                                                $result = Carbon::create($service->date_begin)->format('Y-m-d');
                                                $date = Carbon::parse($result)->addWeeks($service->time_period);

                                                break;

                                            case 'Months':
                                                $result = Carbon::create($service->date_begin)->format('Y-m-d');
                                                $date = Carbon::parse($result)->addMonths($service->time_period);

                                                break;

                                            case 'Years':
                                                $result = Carbon::create($service->date_begin)->format('Y-m-d');
                                                $date = Carbon::parse($result)->addYears($service->time_period);

                                                break;

                                            default:
                                                break;
                                        }

                                        if ($date < Carbon::now()->format('Y-m-d')) {
                                            // desactivar
                                            $service->allow_discount = 0;
                                            $service->save();
                                            Log::debug('Descuento desactivado para el servicio: '.$service->id);
                                        } else {
                                            if ($date == Carbon::now()->format('Y-m-d')) {
                                                PbxServicesReCalculateTrait::calculatePriceService($service);
                                            }
                                        }
                                    }

                                }
                                // Actualización exts (Post creación de factura)
                                if ($service->allow_pbx_packages_update) {
                                    self::updateExtensionsPrice($service);
                                    self::updateAdditionalCharges($service);
                                    self::updateDidsPrice($service);
                                    // Llamar actualizador servicio
                                    PbxServicesReCalculateTrait::calculatePriceService($service, true);
                                    Log::debug('Extensión de servicio actualizada: '.$service->id);
                                }

                                try {
                                    if ($invoice != null) {
                                        PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                                        Log::debug('Factura generada para el servicio: '.$service->id);
                                    }
                                } catch (Exception $e) {
                                    Log::debug($e->getMessage());
                                }

                            }
                        }
                    }

                    // Activación de servicios pendientes
                    self::pendingServiceActivation($company);

                }

            }
        }

        Log::debug('fin del metodo checkpbxservie');

        return 0;
    }

    public static function calculateDiscounts($service, $preSubTotal)
    {
        Log::debug('¿¿Iniciando el cálculo de descuentos.');
        Log::debug($service->pbx_services_number);
        $discountValue = 0;
        // Validar si el servicio permite descuentos y desactivar si no cumplen las reglas
        Log::debug('Verificando si el servicio permite descuentos.');

        if ($service->allow_discount) {
            Log::debug('El servicio permite descuentos.');

            // D - fechas
            // T - periodo
            if ($service->discount_term_type == 'D') {
                Log::debug('El tipo de término de descuento es por fechas.');

                if (! is_null($service->date_to) && ! is_null($service->date_from)) {
                    Log::debug('Las fechas de inicio y fin del descuento están establecidas.');

                    $date = Carbon::now()->format('Y-m-d');
                    Log::debug("La fecha actual es {$date}.");

                    if ($date >= $service->date_from && $date <= $service->date_to) {
                        Log::debug('Estamos dentro del rango de fechas del descuento.');

                        switch ($service->allow_discount_type) {
                            case 'percentage':
                                Log::debug('El tipo de descuento es por porcentaje.');
                                $discountValue = $preSubTotal * $service->allow_discount_value / 100;

                                break;
                            case 'fixed':
                                Log::debug('El tipo de descuento es fijo.');
                                $discountValue = $service->allow_discount_value * 100;

                                break;
                        }
                    }
                    if ($service->date_to < $date) {
                        Log::debug('La fecha de fin del descuento ha pasado, desactivando descuentos.');
                        // Desactivar descuentos
                        $service->allow_discount_value = 0;
                        $service->discount_val = 0;

                        $service->allow_discount = 0;
                        $service->save();
                    }
                }
            }

            if ($service->discount_term_type == 'T') {
                Log::debug('El tipo de término de descuento es por periodo de tiempo.');

                // Días - Semanas - Meses - Años
                $dateTo = '';
                $dateFrom = '';
                $toDay = Carbon::now()->format('Y-m-d');
                Log::debug("La fecha de hoy es {$toDay}.");

                switch ($service->time_period_value) {
                    case 'Days':
                        Log::debug('El valor del periodo de tiempo es en días.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addDays($service->time_period);

                        break;

                    case 'Weeks':
                        Log::debug('El valor del periodo de tiempo es en semanas.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addWeeks($service->time_period);

                        break;

                    case 'Months':
                        Log::debug('El valor del periodo de tiempo es en meses.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addMonths($service->time_period);

                        break;

                    case 'Years':
                        Log::debug('El valor del periodo de tiempo es en años.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addYears($service->time_period);

                        break;

                    default:
                        Log::debug('El valor del periodo de tiempo no es reconocido.');

                        break;
                }

                if ($toDay >= $dateFrom && $toDay <= $dateTo) {
                    Log::debug('Estamos dentro del periodo de tiempo del descuento.');

                    switch ($service->allow_discount_type) {
                        case 'percentage':
                            Log::debug('El tipo de descuento es por porcentaje.');
                            $discountValue = $preSubTotal * $service->allow_discount_value / 100;

                            break;
                        case 'fixed':
                            Log::debug('El tipo de descuento es fijo.');
                            $discountValue = $service->allow_discount_value * 100;

                            break;
                    }
                }

                if ($dateTo < $toDay) {
                    Log::debug('El periodo de tiempo del descuento ha terminado, desactivando descuentos.');
                    // Desactivar
                    $service->allow_discount_value = 0;
                    $service->discount_val = 0;

                    $service->allow_discount = 0;
                    $service->save();
                }
            }
        } else {
            Log::debug('El servicio no permite descuentos, no se aplicará ningún descuento.');
        }
        Log::debug("El valor del descuento calculado es {$discountValue}.");
        Log::debug('¿¿Ifin el cálculo de descuentos.');

        return $discountValue;
    }

    public static function calculateDiscountBool($service, $preSubTotal)
    {

        $discountApplied = false; // Valor inicial cambiado a false
        // Validar si el servicio permite descuentos y desactivar si no cumplen las reglas
        Log::debug('Verificando si el servicio permite descuentos.');

        if ($service->allow_discount) {
            Log::debug('El servicio permite descuentos.');

            // D - fechas
            // T - periodo
            if ($service->discount_term_type == 'D') {
                Log::debug('El tipo de término de descuento es por fechas.');

                if (! is_null($service->date_to) && ! is_null($service->date_from)) {
                    Log::debug('Las fechas de inicio y fin del descuento están establecidas.');

                    $date = Carbon::now()->format('Y-m-d');
                    Log::debug("La fecha actual es {$date}.");

                    if ($date >= $service->date_from && $date <= $service->date_to) {
                        Log::debug('Estamos dentro del rango de fechas del descuento.');
                        $discountApplied = true; // Cambiar a true si se cumple la condición
                    }
                    if ($service->date_to < $date) {
                        Log::debug('La fecha de fin del descuento ha pasado, desactivando descuentos.');
                        // Desactivar descuentos
                        //$service->allow_discount = 0;
                        //  $service->save();
                    }
                }
            }

            if ($service->discount_term_type == 'T') {
                Log::debug('El tipo de término de descuento es por periodo de tiempo.');

                // Días - Semanas - Meses - Años
                $dateTo = '';
                $dateFrom = '';
                $toDay = Carbon::now()->format('Y-m-d');
                \Log::debug("La fecha de hoy es {$toDay}.");

                switch ($service->time_period_value) {
                    case 'Days':
                        \Log::debug('El valor del periodo de tiempo es en días.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addDays($service->time_period);

                        break;

                    case 'Weeks':
                        \Log::debug('El valor del periodo de tiempo es en semanas.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addWeeks($service->time_period);

                        break;

                    case 'Months':
                        \Log::debug('El valor del periodo de tiempo es en meses.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addMonths($service->time_period);

                        break;

                    case 'Years':
                        \Log::debug('El valor del periodo de tiempo es en años.');
                        $dateFrom = Carbon::create($service->date_begin)->format('Y-m-d');
                        $dateTo = Carbon::parse($dateFrom)->addYears($service->time_period);

                        break;

                    default:
                        \Log::debug('El valor del periodo de tiempo no es reconocido.');

                        break;
                }

                if ($toDay >= $dateFrom && $toDay <= $dateTo) {
                    Log::debug('Estamos dentro del periodo de tiempo del descuento.');
                    $discountApplied = true; // Cambiar a true si se cumple la condición
                }

                if ($dateTo < $toDay) {
                    Log::debug('El periodo de tiempo del descuento ha terminado, desactivando descuentos.');
                    // Desactivar
                    $service->allow_discount = 0;
                    $service->save();
                }
            }
        } else {
            Log::debug('El servicio no permite descuentos, no se aplicará ningún descuento.');
        }
        Log::debug('El descuento fue aplicado: '.($discountApplied ? 'Sí' : 'No'));

        return $discountApplied;
    }

    public function generateInvoices($serviceInvoice, $customer, $date_to_invoice, $now)
    {

        Log::debug('-----inicio del metodo generainvoice');
        // Imprimir los parámetros recibidos
        //  \Log::debug("Parámetros: serviceInvoice = " . print_r($serviceInvoice, true) . ", customer = " . print_r($customer, true) . ", date_to_invoice = " . $date_to_invoice . ", now = " . $now);

        switch ($serviceInvoice->term) {
            case 'daily':
                // Si el término es diario
                $date_to_invoice_daily = Carbon::parse($serviceInvoice->renewal_date)->format('Y-m-d');

                // Comparar la fecha de facturación diaria con la fecha actual
                if (strcmp($date_to_invoice_daily, $now) == 0) {
                    // Generar factura
                    Log::debug('Generando factura diaria para el servicio '.$serviceInvoice->id);
                    $invoice = $this->createInvoice($serviceInvoice);

                    try {
                        if ($invoice != null) {
                            PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                        }
                    } catch (Exception $e) {
                        Log::debug($e->getMessage());
                    }

                    return $invoice;
                }

                break;

            case 'weekly':
                // Si el término es semanal
                if ($customer->customerConfigs->invoice_days_before_renewal >= 6) {
                    // Si los días antes de la renovación son mayores o iguales a 6
                    $date_to_invoice_weekly = Carbon::parse($serviceInvoice->renewal_date)->subDays(6)->format('Y-m-d');

                    // Comparar la fecha de facturación semanal con la fecha actual
                    if (strcmp($date_to_invoice_weekly, $now) == 0) {
                        // Generar factura
                        Log::debug('Generando factura semanal para el servicio '.$serviceInvoice->id);
                        $invoice = $this->createInvoice($serviceInvoice);

                        try {
                            if ($invoice != null) {
                                PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                            }
                        } catch (Exception $e) {
                            Log::debug($e->getMessage());
                        }

                        return $invoice;
                    }
                } else {
                    // Si los días antes de la renovación son menores a 6
                    // Comparar la fecha de facturación normal con la fecha actual
                    if (strcmp($date_to_invoice, $now) == 0) {
                        // Generar factura
                        Log::debug('Generando factura normal para el servicio '.$serviceInvoice->id);
                        $invoice = $this->createInvoice($serviceInvoice);

                        try {
                            if ($invoice != null) {
                                PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                            }
                        } catch (Exception $e) {
                            Log::debug($e->getMessage());
                        }

                        return $invoice;
                    }
                }

                break;

            default:
                // Para términos mensuales, trimestrales, etc.
                if (strcmp($date_to_invoice, $now) == 0) {
                    // Generar factura
                    Log::debug('Generando factura para el servicio '.$serviceInvoice->id);
                    $invoice = $this->createInvoice($serviceInvoice);

                    try {
                        if ($invoice != null) {
                            PbxGenerateAvalaraTaxesJob::dispatch($invoice->id);
                        }
                    } catch (Exception $e) {
                        Log::debug($e->getMessage());
                    }

                    return $invoice;
                }

                break;
        }

        Log::debug('-----fin del metodo generainvoice');
    }

    public static function getRenewalDate($service, $current_renewal_date)
    {
        $renewal_date = '';
        switch ($service->term) {
            case PbxServices::TERM_DAILY:
                $renewal_date = $current_renewal_date->addDay();

                break;
            case PbxServices::TERM_WEEKLY:
                $renewal_date = $current_renewal_date->addWeek();

                break;
            case PbxServices::TERM_MONTHLY:
                $renewal_date = $current_renewal_date->addMonth();

                break;
            case PbxServices::TERM_BIMONTHLY:
                $renewal_date = $current_renewal_date->addMonths(2);

                break;
            case PbxServices::TERM_QUARTERLY:
                $renewal_date = $current_renewal_date->addQuarter();

                break;
            case PbxServices::TERM_BIANNUAL:
                $renewal_date = $current_renewal_date->addMonths(6);

                break;
            case PbxServices::TERM_YEARLY:
                $renewal_date = $current_renewal_date->addYear();

                break;
            case PbxServices::TERM_ONE_TIME:
                $renewal_date = Carbon::now();

                break;
            default:
                $renewal_date = now();
        }

        return $renewal_date->format('Y-m-d');
    }

    public static function createInvoice($service)
    {

        Log::debug('Create invoice Servicio inicio: '.$service->pbx_services_number);

        $exist = Invoice::where('pbx_service_id', $service->id)->where('pbxservice_date_prev', $service->date_prev)
            ->where('pbxservice_date_renewal', $service->renewal_date)->first();

        if ($exist != null) {
            Log::debug('Ya existe factura para servicio: '.$service->pbx_services_number);

            return false;
        }

        $prefix = CompanySetting::getSetting('invoice_prefix', $service->company_id);
        $pricepackahe = $service->pbxpackages_price;

        $nextNumber = Invoice::getNextInvoiceNumber($prefix);
        $customerul = User::where('id', $service->customer_id)->first();
        $typecust = 'postpaid';
        if ($customerul != null) {
            $typecust = $customerul->status_payment;
        }

        // Obtener el valor de 'invoice_issuance_period' para la compañía específica
        $invoiceIssuancePeriod = CompanySetting::getSetting('invoice_issuance_period', $service->company_id);

        // Validar si el valor obtenido es nulo, vacío o no definido
        // y asignar 7 a DueDateDays si es así
        if (empty($invoiceIssuancePeriod)) {
            $DueDateDays = 7;
        } elseif (is_numeric($invoiceIssuancePeriod)) {
            // Si es un string numérico, convertir a entero
            if (is_string($invoiceIssuancePeriod) && ctype_digit($invoiceIssuancePeriod)) {
                $DueDateDays = (int)$invoiceIssuancePeriod;
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

        // Inicializamos la variable $discount_val en 0
        $discount_val = 0;
        $allow_discount_value = 0;
        Log::debug('Inicializando la variable de valor de descuento en 0.');

        // Verificamos si el servicio permite descuentos y si el cálculo de descuento devuelve true
        if ($service->allow_discount && self::calculateDiscountBool($service, 0)) {
            Log::debug('El servicio permite descuentos y el cálculo de descuento es verdadero.');

            // Asignamos el valor de descuento permitido del servicio a la variable $allow_discount_value
            $allow_discount_value = $service->allow_discount_value;
            Log::debug("Valor de descuento permitido asignado: {$allow_discount_value}");

            // Asignamos el valor de descuento del servicio a la variable $discount_val
            $discount_val = $service->discount_val;
            Log::debug("Valor de descuento del servicio asignado: {$discount_val}");
        } else {
            Log::debug('El servicio no permite descuentos o el cálculo de descuento es falso.');
        }


        //finde periodo de facturacion

        $next_renewal_date = Carbon::parse($service->renewal_date);

        $next_renewal_date = self::getRenewalDate($service, $next_renewal_date);

        $invoice = Invoice::create([
            'pbx_service_id' => $service->id,
            'invoice_date' => now()->format('Y-m-d'),
            'due_date' => now()->addDays($DueDateDays)->format('Y-m-d'),
            'invoice_number' => $prefix.'-'.$nextNumber,
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'tax_per_item' => 'NO',
            'discount_per_item' => 'NO',
            'notes' => 'Invoice created from services '.$service->pbx_services_number.' - '.$service->pbxPackage->pbx_package_name,
            'discount_type' => $service->allow_discount_type,
            'discount' => $allow_discount_value,
            'discount_val' => $discount_val,
            'sub_total' => 0,
            'total' => 0,
            'tax' => 0,
            'invoice_template_id' => 1,
            'due_amount' => 0,
            'user_id' => $service->customer_id,
            'company_id' => $service->company_id,
            'creator_id' => $service->creator_id,
            'pbxservice_date_prev' => $service->date_prev,
            'pbxservice_date_renewal' => $service->renewal_date,
            'end_period_services' => Carbon::parse($next_renewal_date)->format('Y-m-d'),
            'invoice_pbx_modify' => 1,

        ]);

        do {
            // Generar un nuevo hash único
            $uniquehash = Hashids::connection(Invoice::class)->encode($invoice->id * rand(2, 100));
            // Verificar si el hash ya existe en la base de datos
            $hashExists = Invoice::where('unique_hash', $uniquehash)->exists();
        } while ($hashExists);

        // Una vez que tenemos un hash único, lo asignamos al invoice
        $invoice->unique_hash = $uniquehash;
        $invoice->save();
        Log::debug('Servicio------------------------ factura creada');
        Log::debug('Servicio : '.$service->pbx_services_number);
        Log::debug('term : '.$service->term);
        Log::debug('factura generada : '.$invoice->invoice_number);
        Log::debug('Servicio------------------------ factura creada');

        $total_items = self::createInvoiceItems($invoice, $service);

        $total_extensions = self::createInvoiceExtensions($invoice, $service);
        $count_extensions = self::createInvoiceExtensionscount($invoice, $service);

        $total_did = self::createInvoiceDids($invoice, $service);
        $count_did = self::createInvoiceDidscount($invoice, $service);

        $total_charges = self::createInvoiceAdditionalCharges($invoice, $service);

        $total_apprate = self::createInvoiceCustomAppRate($invoice, $service);

        //Log::debug($total_apprate);

        ///  calculo de cdrs
        $total_cdr = $service->pbxCdrTotalsCurrent()->sum('exclusive_cost') * 100;

        if ($typecust == 'prepaid') {
            $total_cdr = 0;
        }

        // Se actualizan los cdr del servicio con el id de la factura
        $cdr_updated_ids = [];
        foreach ($service->pbxCdrTotalsCurrent as $cdr) {
            $cdr->invoice_id = $invoice->id;
            $cdr->save();
            $cdr_updated_ids[] = $cdr->id;
        }

        // Actualizar tabla history_call_indi con el id de la factura
        if ($service->pbxPackage->status_payment == 'prepaid') {

            \DB::table('history_call_indi')
                ->whereIn('call_detail_register_totals_id', $cdr_updated_ids)
                ->update(['invoice_id' => $invoice->id]);

            $prepaid_totals = \DB::table('history_call_indi')
                ->where('invoice_id', $invoice->id)
                ->selectRaw('sum(amout) as prepaid_amount, sum(taxamount) as tax_prepaid_amount')
                ->first();

            $invoice->prepaid_amount = $prepaid_totals->prepaid_amount;
            $invoice->tax_prepaid_amount = $prepaid_totals->tax_prepaid_amount;
            $invoice->save();
        }

        $pre_sub_total = ($total_items + $total_extensions + $total_did + $total_charges + $total_cdr + $total_apprate + $pricepackahe);
        $discount_value = self::calculateDiscounts($service, $pre_sub_total);
        $sub_total = $pre_sub_total - $discount_value;

        $amount_to_calculate_general_taxes = $sub_total;
        $tax_cdr_amount = 0;

        /// calculo de taxes cdrs para customer post paid
        if ($typecust == 'postpaid') {
            $amount_to_calculate_general_taxes = $sub_total - $total_cdr;
            $deadre = PbxServicesTaxTypesCdr::where('pbx_services_id', $service->id)->whereNull('deleted_at')->get();

            foreach ($deadre as $taxcdr) {
                $pop = $total_cdr * ($taxcdr->percent / 100);

                $invoice->taxes()->create([
                    'tax_type_id' => $taxcdr->tax_types_id,
                    'company_id' => $taxcdr->company_id,
                    'name' => $taxcdr->name,
                    'amount' => $pop,
                    'percent' => $taxcdr->percent,
                    'compound_tax' => $taxcdr->compound_tax,
                ]);

                $tax_cdr_amount = $tax_cdr_amount * $pop;
            }

        }

        $sum_taxes = 0;
        /// calculo de taxes generales
        foreach ($service->pbxServiceTaxTypes as $tax) {

            $amount = $amount_to_calculate_general_taxes * ($tax->percent / 100);

            $invoice->taxes()->create([
                'tax_type_id' => $tax->tax_types_id,
                'company_id' => $tax->company_id,
                'name' => $tax->name,
                'amount' => $amount,
                'percent' => $tax->percent,
                'compound_tax' => $tax->compound_tax,
            ]);

            $sum_taxes += $amount;
        }

        $total = $sub_total + $sum_taxes + $tax_cdr_amount;

        $invoice->pbx_total_items = $total_items;
        $invoice->pbx_total_extension = $total_extensions;
        $invoice->pbx_total_did = $total_did;
        $invoice->pbx_total_aditional_charges = $total_charges;
        $invoice->pbx_total_cdr = $total_cdr;
        $invoice->pbx_total_apprate = $total_apprate;
        $invoice->pbx_packprice = $pricepackahe;
        $invoice->sub_total = $pre_sub_total;
        $invoice->tax = $sum_taxes;
        $invoice->total = $total;
        $invoice->discount_val = $discount_value;
        $invoice->due_amount = $total;

        if (! is_null($service->pbxPackage)) {
            if (! is_null($service->pbxPackage->profileExtensions)) {
                $invoice->pbx_extension_price = $service->pbxPackage->profileExtensions->rate;
            }
        }

        $invoice->count_extension = $count_extensions;
        $invoice->count_did = $count_did;
        $invoice->save();

        Log::debug('Create invoice Servicio fin: '.$service->pbx_services_number);

        PbxServicesReCalculateTrait::calculatePriceService($service, true);

        return $invoice;
    }

    public static function createInvoiceItems($invoice, $service)
    {
        foreach ($service->items as $item) {
            $invoice_item = $invoice->items()->create($item->toArray());
            $invoice_item->item_id = $item->items_id;

            //valida la existencia del id
            $itemobject = Item::where('id', $invoice_item->item_id)->first();
            if ($invoice_item->item_id == 0 || $itemobject == null) {
                $invoice_item->item_id = null;
            }
            $invoice_item->save();

            //faltaria guardar los taxes del servicio, la asociacion se debe hacer por $invoice_item->id == invoice_item_id
        }

        return $service->items()->sum('total');
    }

    public static function createInvoiceExtensions($invoice, $service)
    {
        $serviceExtensions = PbxServicesExtensions::with('extension')
            ->where('pbx_service_id', '=', $service->id)
            ->get();

        $profile = $service->pbxPackage->profileExtensions;

        $extensions = $serviceExtensions->map(function ($serv_ext) use ($profile, $service) {
            $priceExtension = PbxServicesExtensions::where('pbx_service_id', $service->id)->where('id', $serv_ext->id)->first();

            $extension = $serv_ext->extension;

            if ($profile != null) {
                $extension['profile_id'] = $profile->id;
                $extension['profile_name'] = $profile->name;

                if ($serv_ext->date_prorate != null && $serv_ext->invoiced_prorate != 0) {
                    $extension['profile_rate'] = $serv_ext->prorate / 100;
                    $service->total_prorate = 0;
                    $serv_ext->old_prorate = $serv_ext->prorate;
                    $serv_ext->old_date_prorate = $serv_ext->date_prorate;
                    $serv_ext->prorate = null;
                    $serv_ext->date_prorate = null;
                    $serv_ext->invoiced_prorate = 0;
                    $serv_ext->save();
                } else {
                    $extension['profile_rate'] = floatval($profile->rate);

                    if ($priceExtension != null && $priceExtension->price != null) {
                        // convert string to number
                        $extension['profile_rate'] = floatval($priceExtension->price);
                    }
                }

                if ($priceExtension != null) {
                    $extension['idTablePivot'] = $priceExtension->id;
                }
            } else {
                $extension['profile_id'] = null;
                $extension['profile_name'] = null;
                $extension['profile_rate'] = 0;
            }

            return $extension;
        });

        foreach ($extensions as $extension) {
            $invoice_extension = $invoice->extensions()
                ->create([
                    'pbx_extension_id' => $extension->id,
                    'template_extension_id' => $extension->profile_id,
                    'company_id' => $extension->company_id,
                    'creator_id' => 0,
                    'pbx_extension_name' => $extension->name,
                    'pbx_extension_ext' => $extension->ext,
                    'pbx_extension_email' => $extension->email,
                    'pbx_extension_ua_fullname' => $extension->ua_fullname,
                    'template_extension_name' => $extension->profile_name,
                    'template_extension_rate' => $extension->profile_rate,
                ]);
        }

        // Extensions_group
        $extensions_group = DB::table('invoice_extensions')->where('invoice_id', $invoice->id)
            ->selectRaw('template_extension_name as name, count(*) as quantity, template_extension_rate as price')
            ->groupBy('template_extension_rate')->get();

        foreach ($extensions_group as $group) {
            DB::table('invoice_pbx_extension_detail')->insert([
                'invoice_id' => $invoice->id,
                'name' => $group->name,
                'quantity' => $group->quantity,
                'price' => $group->price,
                'total' => $group->quantity * $group->price,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }
        //

        return $extensions->sum('profile_rate') * 100;
    }

    public static function createInvoiceExtensionscount($invoice, $service)
    {
        $cont = 0;
        $serviceExtensions = PbxServicesExtensions::with('extension')
            ->where('pbx_service_id', '=', $service->id)
            ->get();

        // $extensions = $serviceExtensions->pluck('extension');

        $profile = $service->pbxPackage->profileExtensions;

        $extensions = $serviceExtensions->map(function ($serv_ext) use ($profile, $service) {

            $priceExtension = PbxServicesExtensions::where('pbx_service_id', $service->id)->where('pbx_extension_id', $serv_ext->id)->first();

            $extension = $serv_ext->extension;

            if ($profile != null) {
                $extension['profile_id'] = $profile->id;
                $extension['profile_name'] = $profile->name;

                if ($serv_ext->date_prorate != null && $serv_ext->invoiced_prorate != 0) {
                    $extension['profile_rate'] = $serv_ext->prorate / 100;
                    $service->total_prorate = 0;
                    $serv_ext->old_prorate = $serv_ext->prorate;
                    $serv_ext->old_date_prorate = $serv_ext->date_prorate;
                    $serv_ext->prorate = null;
                    $serv_ext->date_prorate = null;
                    $serv_ext->invoiced_prorate = 0;
                    $serv_ext->save();

                } else {
                    $extension['profile_rate'] = $profile->rate;
                }

                if ($priceExtension != null) {
                    $extension['idTablePivot'] = $priceExtension->id;
                }

                if ($priceExtension != null && $priceExtension->price != null) {
                    // convert string to number
                    $extension['profile_rate'] = floatval($priceExtension->price);
                } else {
                    $extension['profile_rate'] = floatval($profile->rate);
                }

            } else {
                $extension['profile_id'] = null;
                $extension['profile_name'] = null;
                $extension['profile_rate'] = 0;
            }

            return $extension;
        });

        foreach ($extensions as $extension) {
            $cont++;
        }

        return $cont;
    }

    public static function createInvoiceDids($invoice, $service)
    {
        $service_did = PbxServicesDID::with('did', 'customDid')
            ->where('pbx_service_id', '=', $service->id)
            ->get();

        $profile = $service->pbxPackage->profileDid;

        $dids = $service_did->map(function ($serv_did) use ($profile, $service) {

            $did = ! is_null($serv_did->customDid) ? $serv_did->customDid : $serv_did->did;
            $general_price = 0;

            if (! is_null($serv_did->customDid)) {

                $did->id = $serv_did->did->id;
                $did->number = $serv_did->did->number;
                $did->server = $serv_did->did->server;
                $did->trunk = $serv_did->did->trunk;
                $did->type = $serv_did->did->type;
                $did->custom_did_id = $serv_did->custom_did_id;
                $did->profile_id = null;
                $did->profile_name = null;

                if ($serv_did->date_prorate != null && $serv_did->invoiced_prorate != 0) {
                    $general_price = $serv_did->prorate / 100;
                    //
                    $did->custom_did_rate = 0;
                    $did->profile_rate = $serv_did->prorate / 100;
                    $service->total_prorate = 0;
                    $serv_did->old_prorate = $serv_did->prorate;
                    $serv_did->old_date_prorate = $serv_did->date_prorate;
                    $serv_did->prorate = null;
                    $serv_did->date_prorate = null;
                    $serv_did->invoiced_prorate = 0;
                    $serv_did->save();
                } elseif ($serv_did->price != null) {
                    $general_price = $serv_did->price;
                    //
                    $did->profile_rate = $serv_did->price;
                    $did->custom_did_rate = 0;
                } else {
                    $general_price = $serv_did->customDid->rate_per_minute;
                    $did->custom_did_rate = $serv_did->customDid->rate_per_minute;
                    $did->profile_rate = 0;
                }
            } else {
                $did->custom_did_id = null;
                $did->custom_did_rate = 0;
                if ($profile != null) {
                    $did->profile_id = $profile->id;
                    $did->profile_name = $profile->name;

                    if ($serv_did->date_prorate != null && $serv_did->invoiced_prorate != 0) {
                        $general_price = $serv_did->prorate / 100;
                        //
                        $did->profile_rate = $serv_did->prorate / 100;
                        $service->total_prorate = 0;
                        $serv_did->old_prorate = $serv_did->prorate;
                        $serv_did->old_date_prorate = $serv_did->date_prorate;
                        $serv_did->prorate = null;
                        $serv_did->date_prorate = null;
                        $serv_did->invoiced_prorate = 0;
                        $serv_did->save();
                    } else {
                        $general_price = $serv_did->price;
                        //
                        $did->profile_rate = $serv_did->price;
                    }

                } else {
                    $did->profile_id = null;
                    $did->profile_name = null;
                    $did->profile_rate = 0;
                }
            }

            $did['name_prefix'] = $serv_did->name_prefix;
            $did['price'] = $general_price;

            return $did;
        });

        foreach ($dids as $did) {

            $name_template = 'Default template';
            if ($did->name_prefix) {
                if ($did->name_prefix != null) {
                    $name_template = $did->name_prefix;
                }

            }
            $invoice_did = $invoice->dids()
                ->create([
                    'pbx_did_id' => $did->id,
                    'template_did_id' => $did->profile_id,
                    'company_id' => $did->company_id,
                    'creator_id' => 0,
                    'pbx_did_number' => $did->number,
                    'pbx_did_server' => $did->server,
                    'pbx_did_trunk' => $did->trunk,
                    'pbx_did_type' => $did->type,
                    'template_did_name' => $did->profile_name,
                    'template_did_rate' => $did->custom_did_id == null ? $did->price : null,
                    'custom_did_id' => $did->custom_did_id,
                    'custom_did_rate' => $did->custom_did_id != null ? $did->price : null,
                    'name_prefix' => $name_template,
                    'price' => $did->price,
                ]);
        }

        // Dids_groups
        $dids_group = DB::table('invoice_dids')->where('invoice_id', $invoice->id)
            ->selectRaw('name_prefix as name, count(*) as quantity, price')
            ->groupBy('name_prefix', 'price')->get();

        foreach ($dids_group as $group) {
            DB::table('invoice_pbx_did_detail')->insert([
                'invoice_id' => $invoice->id,
                'name' => $group->name,
                'quantity' => $group->quantity,
                'price' => $group->price,
                'total' => $group->quantity * $group->price,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ]);
        }

        //
        return ($dids->sum('profile_rate') + $dids->sum('custom_did_rate')) * 100;
    }

    public static function createInvoiceDidscount($invoice, $service)
    {
        $cont = 0;
        $service_did = PbxServicesDID::with('did', 'customDid')
            ->where('pbx_service_id', '=', $service->id)
            ->get();

        // $dids = $service_did->pluck('did');

        $profile = $service->pbxPackage->profileDid;

        $dids = $service_did->map(function ($serv_did) use ($profile, $service) {

            $did = ! is_null($serv_did->customDid) ? $serv_did->customDid : $serv_did->did;

            if (! is_null($serv_did->customDid)) {

                $did->id = $serv_did->did->id;
                $did->number = $serv_did->did->number;
                $did->server = $serv_did->did->server;
                $did->trunk = $serv_did->did->trunk;
                $did->type = $serv_did->did->type;
                $did->custom_did_id = $serv_did->custom_did_id;
                $did->profile_id = null;
                $did->profile_name = null;

                if ($serv_did->date_prorate != null && $serv_did->invoiced_prorate != 0) {
                    $did->custom_did_rate = 0;
                    $did->profile_rate = $serv_did->prorate / 100;
                    $service->total_prorate = 0;
                    $serv_did->old_prorate = $serv_did->prorate;
                    $serv_did->old_date_prorate = $serv_did->date_prorate;
                    $serv_did->prorate = null;
                    $serv_did->date_prorate = null;
                    $serv_did->invoiced_prorate = 0;
                    $serv_did->save();
                } else {
                    $did->custom_did_rate = $serv_did->customDid->rate_per_minute;
                    $did->profile_rate = 0;
                }

            } else {
                $did->custom_did_id = null;
                $did->custom_did_rate = 0;
                if ($profile != null) {
                    $did->profile_id = $profile->id;
                    $did->profile_name = $profile->name;

                    if ($serv_did->date_prorate != null && $serv_did->invoiced_prorate != 0) {
                        $did->profile_rate = $serv_did->prorate / 100;
                        $service->total_prorate = 0;
                        $serv_did->old_prorate = $serv_did->prorate;
                        $serv_did->old_date_prorate = $serv_did->date_prorate;
                        $serv_did->prorate = null;
                        $serv_did->date_prorate = null;
                        $serv_did->invoiced_prorate = 0;
                        $serv_did->save();
                    } else {
                        $did->profile_rate = $profile->did_rate;
                    }

                } else {
                    $did->profile_id = null;
                    $did->profile_name = null;
                    $did->profile_rate = 0;
                }
            }

            $did['name_prefix'] = $serv_did->name_prefix;

            return $did;
        });

        foreach ($dids as $did) {
            $cont++;
        }

        return $cont;
    }

    public static function createInvoiceAdditionalCharges($invoice, $service)
    {
        $pbx_additional_charges = PbxAdditionalCharge::where('pbx_service_id', $service->id)->get();

        $total_encode = DB::table('pbx_additional_charges')->where('pbx_service_id', $service->id)
            ->selectRaw('sum(amount * quantity) as total')
            ->first();

        $total = (array)$total_encode;

        foreach ($pbx_additional_charges as $charge) {
            if ($charge->profile_extension_id != null) {
                $profile_name = ProfileExtensions::where('id', $charge->profile_extension_id)->first('name')->name;
            }
            if ($charge->profile_did_id != null) {
                $profile_name = ProfileDid::where('id', $charge->profile_did_id)->first('name')->name;
            }

            $invoice_additional_charge = $invoice->additionalCharges()
                ->create([
                    'additional_charge_id' => $charge->additional_charge_id,
                    'company_id' => $charge->company_id,
                    'creator_id' => 0,
                    'additional_charge_name' => $charge->description,
                    'additional_charge_amount' => $charge->amount,
                    'additional_charge_type' => $charge->profile_extension_id != null ? 'Extension' : 'Did',
                    'template_name' => $profile_name,
                    'qty' => $charge->quantity,
                    'total' => $charge->amount * $charge->quantity,
                    'profile_extension_id' => $charge->profile_extension_id,
                    'profile_did_id' => $charge->profile_did_id,
                ]);
        }

        return $total['total'] * 100;
    }

    public static function createInvoiceCustomAppRate($invoice, $service)
    {

        $total = 0;

        $apps = PbxServicesAppRate::where('pbx_service_id', $service->id)->where('costo', '>', 0)->whereNull('deleted_at')->get();
        foreach ($apps as $app) {

            $invoiceapp = new InvoiceAppRates();
            $invoiceapp->invoice_id = $invoice->id;
            $invoiceapp->app_name = $app->app_name;
            $invoiceapp->quantity = $app->quantity;
            $invoiceapp->costo = $app->costo;
            $invoiceapp->pbx_package_id = $app->pbx_package_id;
            $invoiceapp->pbx_service_id = $app->pbx_service_id;
            $invoiceapp->save();
            $total = $total + $app->costo;

        }

        return $total * 100;
    }

    public static function pendingServiceActivation($company)
    {
        $pending_services = PbxServices::where('company_id', $company->id)
            ->where('status', 'P')
            ->whereDate('date_begin', '<=', Carbon::now())
            ->get();

        foreach ($pending_services as $service) {
            $service->status = 'A';
            $service->save();
        }
    }

    public static function updateExtensionsPrice($service)
    {
        $pbx_service_extensions_ids = PbxServicesExtensions::where('pbx_service_id', $service->id)->pluck('id');

        // template_ext Price
        $profile_extensions_rate = \DB::table('pbx_services')
            ->where('pbx_services.id', $service->id)
            ->join('pbx_packages', 'pbx_packages.id', '=', 'pbx_services.pbx_package_id')
            ->join('profile_extensions', 'pbx_packages.template_extension_id', '=', 'profile_extensions.id')
            ->select('profile_extensions.rate as rate')
            ->first();
        $price = (array)$profile_extensions_rate;
        //

        foreach ($pbx_service_extensions_ids as $extension_id) {
            $extension = PbxServicesExtensions::find($extension_id);

            if ($extension) {
                $extension->price = $price['rate'];
                $extension->save();
            }
        }
    }

    public static function updateDidsPrice($service)
    {
        $profileDid = $service->pbxPackage->profileDid;

        if ($profileDid) {
            // update fields (custom_did_id => null and price => 0)
            PbxServicesDID::where('pbx_service_id', $service->id)->update(['price' => 0, 'custom_did_id' => null]);

            // template_did_id
            $profileDidId = $profileDid->id;

            // pluck (profile_did_custom_did_groups -> custom_did_group_id)
            $pluck_custom_did_groups = DB::table('profile_did_custom_did_groups')->where('profile_did_id', $profileDidId)
                ->whereNull('deleted_at')
                ->pluck('custom_did_group_id');

            if ($pluck_custom_did_groups->isNotEmpty()) {
                // pluck (toll_free_custom_did_group -> toll_free_did_id)
                $pluck_toll_free_custom_did_group = DB::table('toll_free_custom_did_group')
                    ->whereIn('custom_did_group_id', $pluck_custom_did_groups)
                    ->whereNull('deleted_at')
                    ->pluck('toll_free_did_id');

                if ($pluck_toll_free_custom_did_group->isNotEmpty()) {
                    // pluck (pbx_service_id -> pbx_did_id)
                    $pluck_pbx_services_did_id = PbxServicesDID::where('pbx_service_id', $service->id)->pluck('pbx_did_id');

                    // custom did ordenados por lenght en el campo prefix
                    $custom_dids = ProfileDidTollFree::whereIn('id', $pluck_toll_free_custom_did_group)
                        ->orderByRaw('CHAR_LENGTH(prefijo) DESC')
                        ->get();

                    // get pbx_dids pertenecientes al servicio
                    $pbx_dids = PbxDID::whereIn('id', $pluck_pbx_services_did_id)->get();

                    $ids_discarded = [];

                    foreach ($pbx_dids as $pbx_did) {
                        $exists_match = false;

                        foreach ($custom_dids as $custom_did) {
                            if (! $exists_match) {
                                // Match search between did and custom_did
                                $match = PbxDID::whereNotIn('id', $ids_discarded)
                                    ->where('number', 'like', "%{$custom_did->prefijo}%")
                                    ->first();
                                if ($match) {
                                    $exists_match = true;
                                    array_push($ids_discarded, $match->id);

                                    $PbxServicesDID = PbxServicesDID::where('pbx_service_id', $service->id)
                                        ->where('pbx_did_id', $pbx_did->id)
                                        ->whereNull('deleted_at')
                                        ->first();

                                    if ($PbxServicesDID) {
                                        $PbxServicesDID->custom_did_id = $custom_did->id;
                                        $PbxServicesDID->price = $custom_did->rate_per_minute;
                                        $PbxServicesDID->save();
                                    }
                                }
                            }
                        }

                        // Add template price to did no match
                        if (! $exists_match) {
                            $PbxServicesDID = PbxServicesDID::where('pbx_service_id', '=', $service->id)
                                ->where('pbx_did_id', '=', $pbx_did->id)
                                ->whereNull('deleted_at')
                                ->first();

                            if ($PbxServicesDID) {
                                $PbxServicesDID->price = $profileDid->did_rate;
                                $PbxServicesDID->save();
                            }
                        }
                    }
                }
            }
        }
    }

    public static function updateAdditionalCharges($service)
    {
        $profileExtensionId = '';
        $profileDidId = '';

        $pbxServices = PbxServices::where('status', '!=', 'C')->where('id', $service->id)
            ->orderBy('created_at', 'asc')
            ->get();

        foreach ($pbxServices as $pbxService) {
            try {
                //
                $profileExtension = $pbxService->pbxPackage->profileExtensions;
                if (! is_null($profileExtension)) {
                    $profileExtensionId = $profileExtension->id;
                }

                // get all services extensions with value price where is null
                $extensions = $pbxService->pbxServiceExtensions->whereNull('price');
                $countExtension = $pbxService->pbxServiceExtensions->count();
                if (! $extensions->isEmpty()) {
                    // get price of the template of the extension
                    $profileExtension = $pbxService->pbxPackage->profileExtensions;
                    $rateExtension = $profileExtension->rate;
                    $profileExtensionId = $profileExtension->id;
                }

                $profileDid = $pbxService->pbxPackage->profileDid;
                if (! is_null($profileDid)) {
                    $profileDidId = $profileDid->id;
                }

                // get all services did with value price where is null
                $dids = $pbxService->pbxServiceDids->whereNull('price');
                $countDids = $pbxService->pbxServiceDids->count();

                if (! $dids->isEmpty()) {
                    // get price of the template of the did
                    $profileDid = $pbxService->pbxPackage->profileDid;
                    $rateDid = $profileDid->did_rate;
                    $profileDidId = $profileDid->id;
                }

                $addChargesExtension = DB::table('aditional_charges')->where('status', 1)
                    ->whereNull('deleted_at')
                    ->where('profile_extension_id', $profileExtensionId)
                    ->get();

                $addChargesDid = DB::table('aditional_charges')->where('status', 1)
                    ->whereNull('deleted_at')
                    ->where('profile_did_id', $profileDidId)
                    ->get();

                PbxAdditionalCharge::where('pbx_service_id', $pbxService->id)->delete();

                if (! $addChargesExtension->isEmpty()) {
                    foreach ($addChargesExtension as $chargeExtension) {
                        $total = $chargeExtension->amount * $countExtension;
                        $dataExtension = json_decode(json_encode($chargeExtension), true);
                        $dataExtension['total'] = $total;
                        $dataExtension['quantity'] = $countExtension;
                        $dataExtension['pbx_service_id'] = $pbxService->id;
                        $dataExtension['additional_charge_id'] = $chargeExtension->id;

                        try {
                            PbxAdditionalCharge::create($dataExtension);
                        } catch (Throwable $th) {
                            Log::debug('PbxAdditionalCharge::create', ['result' => $th, 'data' => $dataExtension]);
                        }
                    }
                }

                if (! $addChargesDid->isEmpty()) {
                    foreach ($addChargesDid as $chargeDid) {
                        $total = $chargeDid->amount * $countDids;
                        $dataDid = json_decode(json_encode($chargeDid), true);
                        $dataDid['total'] = $total;
                        $dataDid['quantity'] = $countDids;
                        $dataDid['pbx_service_id'] = $pbxService->id;
                        $dataDid['additional_charge_id'] = $chargeDid->id;

                        try {
                            PbxAdditionalCharge::create($dataDid);
                        } catch (Throwable $th) {
                            Log::debug('PbxAdditionalCharge::create', ['result' => $th, 'data' => $dataDid]);
                        }
                    }
                }

            } catch (Throwable $th) {
                Log::debug('updateAdditionalCharges', ['error' => $th]);
            }
        }

    }
}
