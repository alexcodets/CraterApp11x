<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\CustomerPackage;
use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Console\Command;

class SuspendService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suspend:service';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'suspend service';

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

        //se obtienen las compañias con procesos activos
        $company_ids_with_active_job = CompanySetting::where('option', 'allow_suspension_packages_job')
            ->where('value', 1)
            ->pluck('company_id');

        if (count($company_ids_with_active_job) != 0) {
            //obtengo hora actual
            $time = Carbon::now()->format('H:i');

            //se obtienen las compañias que coincidan con la fecha y hora
            $company_ids_match_time = CompanySetting::whereIn('company_id', $company_ids_with_active_job)
                ->where('option', 'time_run_suspension_packages_job')
                ->where('value', $time)
                ->pluck('company_id');
            if (count($company_ids_match_time) != 0) {
                $customerid = CustomerConfig::where("customer_id", "!=", 0)->whereIN('company_id', $company_ids_match_time)->pluck('customer_id');
                if (count($customerid) != 0) {
                    $customerlist = User::where("role", "customer")->whereNull('users.deleted_at')->where("status_customer", "A")->whereIN("users.id", $customerid)
                        ->join('customer_configs', 'customer_configs.customer_id', '=', 'users.id')
                        ->select('users.id', 'customer_configs.auto_debit_days_before_due', 'customer_configs.suspend_services_days_after_due', 'customer_configs.auto_debit_attempts')
                        ->get();

                    foreach ($customerlist as $custl) {
                        // $due_date = Carbon::now()->addDays($custl->auto_debit_days_before_due)->format('Y-m-d');
                        $due_date = Carbon::now()->addDays($custl->suspend_services_days_after_due)->format('Y-m-d');

                        $invoice = Invoice::where("user_id", $custl->id)->where("status", "=", "OVERDUE")->where("paid_status", "!=", "PAID")->where("due_date", $due_date)->whereNull('deleted_at')->where("customer_packages_id", "!=", null)->get();
                        // procesar factura para suspender servicio
                        self::processinvoices($invoice, $custl);
                    }
                }

            }

        }
    }

    public static function processinvoices($invoices, $cust)
    {
        foreach ($invoices as $invoice) {
            $customerPackage = CustomerPackage::find($invoice['customer_packages_id']);
            $customerPackage->update(['status' => 'S']);
        }
    }
}
