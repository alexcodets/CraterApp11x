<?php

namespace Crater\Console\Commands;

use Crater\Models\User;
use Crater\Services\Payment\InvoiceAutoDebitService;
use Crater\Services\Payment\PaymentService;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class InvoiceAutoDebitChargeOverdue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:AutoDebitChargeOverdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'InvoiceAutoDebitCharge';

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
        //User List
        $customers = User::query()
            ->whereHas('customerConfig', function (Builder $query) {
                $query->where('enable_auto_debit', 1);
            })->whereHas('companySettings', function (Builder $query) {
                $query->where('option', 'allow_autodebit_customer_job')->where('value', 1);
            })->whereHas('invoices', function (Builder $query) {
                $query->where('status', 'OVERDUE')->where('paid_status', '!=', 'PAID');
            })->whereHas('paymentAccount', function (Builder $query) {
                $query->where('main_account', '=', '1')
                    ->where('status', '=', 'A')
                    ->whereNull('deleted_at');
            })->with([
                'paymentAccount' => function ($query) {
                    $query->where('main_account', '=', '1')
                        ->where('status', '=', 'A')
                        ->whereNull('deleted_at');
                },
                'invoices' => function ($query) {
                    $query->where('status', 'OVERDUE')->where('paid_status', '!=', 'PAID')
                        ->where('not_charge_automatically', 0)
                        ->orderBy('pbx_service_id', 'desc')
                        ->orderBy('customer_packages_id', 'desc')
                        ->orderBy('due_date', 'asc');
                },
            ])->customer()->customerActive()->get();

        Log::debug('----------------------------AutoDebitChargeOverdue Start---------------------------- ');
        if ($customers->isEmpty()) {
            Log::debug('There was not match for the search inside AutoDebitChargeOverdue');

            return self::SUCCESS;
        }


        try {
            foreach ($customers as $customer) {
                $this->process($customer);
            }
        } catch (Exception $e) {
            Log::error('Se ha producido un error al procesar un cliente: ');
            Log::debug($e->getMessage());
            // Manejo adicional del error si es necesario
        }
        Log::debug('-----------------------------AutoDebitChargeOverdue End----------------------------- ');

        return self::SUCCESS;

        // PbxService, PackageService, Normal.

    }

    public function process(User $customer)
    {
        // Verificación de que el cliente no es null
        Log::debug("*** Iniciando el procesamiento del cliente con ID: {$customer->customcode} ***");

        // Creación de instancias de servicios necesarios para el proceso
        $paymentService = new PaymentService($customer, $customer->paymentAccount);
        Log::debug("Servicio de pago instanciado para el cliente con ID: {$customer->id}");

        $service = new InvoiceAutoDebitService($customer, $paymentService);
        Log::debug("Servicio de débito automático de facturas instanciado para el cliente con ID: {$customer->id}");

        // Carga de facturas pendientes del cliente que cumplen con los criterios establecidos
        $customer->load([
            'invoices' => function ($query) {
                $query->where('status', 'OVERDUE')
                    ->where('paid_status', '!=', 'PAID')
                    ->where('not_charge_automatically', 0)
                    ->orderBy('pbx_service_id', 'desc')
                    ->orderBy('customer_packages_id', 'desc')
                    ->orderBy('due_date', 'asc')
                    ->limit(10);
            },
        ]);

        // Verificación de que hay facturas para procesar
        if ($customer->invoices->isNotEmpty()) {
            Log::debug("*** Proceso de pago de facturas iniciado para el cliente con ID: {$customer->customcode} ***");
            $service->payInvoices($customer->invoices);
        } else {
            Log::info("No hay facturas pendientes para el cliente con ID: {$customer->customcode}");
        }
    }
}
