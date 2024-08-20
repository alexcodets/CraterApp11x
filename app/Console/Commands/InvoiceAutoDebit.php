<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\User;
use Crater\Services\Payment\InvoiceAutoDebitService;
use Crater\Services\Payment\InvoiceAutoPaymentService;
use Crater\Services\Payment\PaymentService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Log;
use Throwable;

class InvoiceAutoDebit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:Autodebit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoice auto debit';

    protected InvoiceAutoPaymentService $service;

    protected InvoiceAutoDebitService $autoDebitService;

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
        try {
            $customers = $this->getCustomerList();
        } catch (Throwable $th) {
            Log::debug('Error inside InvoiceAutoDebit');
            Log::debug($th->getMessage());

            return 0;
        }

        if ($customers->isEmpty()) {
            Log::debug('There was not match for the search inside InvoiceAutoDebit');
            Log::debug('\n Hola \n ');

            return self::SUCCESS;
        }
        Log::debug('----------------------------InvoiceAutoDebit Start---------------------------- ');
        foreach ($customers as $customer) {
            $this->process($customer);
        }
        Log::debug('-----------------------------InvoiceAutoDebit End----------------------------- ');

        return self::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function getCustomerList()
    {
        Log::debug('Iniciando el método getCustomerList.');

        // Obtengo la hora actual
        $time = now()->format('H:i');
        Log::debug('Hora actual:');
        Log::debug($time);

        // Obtengo las compañías con débito automático habilitado
        $companyWithAutoDebit = CompanySetting::where('option', 'allow_autodebit_customer_job')
            ->where('value', 1)
            ->pluck('company_id');
        Log::debug('Compañías con débito automático: '.json_encode($companyWithAutoDebit));

        if (count($companyWithAutoDebit) == 0) {
            Log::error('No hay compañías con trabajo de débito automático.');

            throw new Exception('There is not company with auto debit job');
        }

        // Obtengo las compañías que coinciden con la fecha y hora para ejecutar el trabajo
        $companyWithActiveJobAndTime = CompanySetting::whereIn('company_id', $companyWithAutoDebit)
            ->where('option', 'time_run_autodebit_customer_job')
            ->where('value', $time)
            ->pluck('company_id');
        Log::debug('Compañías con trabajos activos y horario coincidente: ');
        Log::debug($companyWithActiveJobAndTime);

        if ($companyWithActiveJobAndTime->isEmpty()) {
            Log::error('No hay compañías con trabajos activos en este momento.');

            throw new Exception('There is not company with active jobs');
        }

        // Obtengo los IDs de los clientes habilitados para débito automático
        $customersId = CustomerConfig::where('enable_auto_debit', 1)
            ->where('customer_id', '!=', 0)
            ->whereIn('company_id', $companyWithActiveJobAndTime)
            ->pluck('customer_id');
        Log::debug('IDs de clientes obtenidos: ');
        Log::debug($customersId);

        if (count($customersId) == 0) {
            Log::error('No hay clientes con ID para procesar.');

            throw new Exception('There is not ID');
        }

        Log::debug('Finalizando el método getCustomerList.');

        return $this->getCustomers($customersId);
    }

    public function getCustomers(Collection $customersId)
    {
        return User::where('role', 'customer')
            ->whereNull('deleted_at')
            ->where('status_customer', 'A')
            ->whereIn('status_payment', ['prepaid', 'postpaid'])
            ->whereIN('id', $customersId)
            ->whereHas('paymentAccount', function (Builder $query) {
                $query->where('main_account', '=', '1')
                    ->where('status', '=', 'A')
                    ->whereNull('deleted_at');
            })
            ->with([
                'customerConfig:id,customer_id,auto_debit_days_before_due,auto_debit_attempts,auto_apply_credits', 'paymentAccount', 'company',
            ])->with([
            'paymentAccount' => function ($query) {
                $query->where('main_account', '=', '1')
                    ->where('status', '=', 'A')
                    ->whereNull('deleted_at');
            },
        ])
            ->get();

    }

    /**
     * Procesa los pagos de las facturas del cliente automáticamente.
     *
     * @param User $customer El cliente cuyas facturas serán procesadas.
     */
    public function process(User $customer)
    {
        // Inicializa el servicio de pago con la cuenta de pago del cliente.
        Log::debug("Inicializando PaymentService para el cliente: {$customer->customcode}");
        $paymentService = new PaymentService($customer, $customer->paymentAccount);

        // Crea el servicio de débito automático de facturas.
        Log::debug("Creando InvoiceAutoDebitService para el cliente: {$customer->customcode}");
        $service = new InvoiceAutoDebitService($customer, $paymentService);

        // Obtiene las facturas pendientes del cliente.
        Log::debug("Obteniendo facturas pendientes para el cliente: {$customer->customcode}");
        $invoices = $this->getInvoice($customer);

        // Registra el inicio del procesamiento del cliente.
        Log::debug("Procesando Cliente: {$customer->id}");
        // Registra el número total de facturas a procesar.
        Log::debug("Total de Facturas: {$invoices->count()}");

        // Verifica si hay facturas para procesar.
        if ($invoices->isEmpty()) {
            Log::debug("No hay facturas para procesar para el cliente: {$customer->customcode}");
        } else {
            // Realiza el pago de las facturas.
            Log::debug("Iniciando el pago de facturas para el cliente: {$customer->customcode}");
            $service->payInvoices($invoices);
            Log::debug("Pago de facturas completado para el cliente: {$customer->customcode}");
        }
    }

    public function getInvoice($customer)
    {
        $dueDate = Carbon::now()->addDays($customer->customerConfig->auto_debit_days_before_due)->format('Y-m-d');

        return Invoice::where('user_id', $customer->id)->where('status', '!=', 'DRAFT')
            ->where('status', '!=', 'SAVE_DRAFT')
            ->where('status', '!=', 'COMPLETED')->where('paid_status', '!=', 'PAID')
            ->where('due_date', $dueDate)->where('not_charge_automatically', 0)->whereNull('deleted_at')->limit(10)->get();
    }
}
