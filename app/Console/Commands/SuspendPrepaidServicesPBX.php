<?php

namespace Crater\Console\Commands;

use Crater\Models\Invoice;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Crater\Pbxware\Service\PbxExtensionSuspendService;
use Crater\Pbxware\Service\PbxTenantSuspendService;
use Crater\Traits\SendEmailsTrait;
use Illuminate\Console\Command;
//traits
use Log;

class SuspendPrepaidServicesPBX extends Command
{
    use SendEmailsTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SuspendPrepaidServicesPBX';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Suspende/activa servicios prepago con balance 0';

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
        $customerlist = User::where("role", "customer")
            ->whereNull('users.deleted_at')
            ->where("users.status_customer", "A")
            ->where("users.status_payment", "prepaid")
            ->get();

        foreach ($customerlist as $cust) {

            if ($cust->auto_debit == 1 || $cust->auto_debit == true || $cust->auto_debit == "1") {
                if ($cust->balance >= $cust->minimun_balance && $cust->balance >= 1) {

                    $pbxservi = PbxServices::where("status", "S")->where("customer_id", $cust->id)->get();

                    foreach ($pbxservi as $pbx) {
                        $val = Invoice::where("pbx_service_id", $pbx->id)->where("status", "!=", "DRAFT")->where("status", "!=", "SAVE_DRAFT")->where("status", "!=", "COMPLETED")->where("paid_status", "!=", "PAID")->whereNull('deleted_at')->get()->count();

                        if ($val == 0) {
                            //enviar correo
                            if ($pbx->suspension_type == "T") {
                                $controller = new PbxTenantSuspendService();
                                $response = $controller->unsuspend($pbx->id);
                            } else {
                                $controller = new PbxExtensionSuspendService();
                                $response = $controller->unsuspend($pbx->id);
                            }

                            $pbx->status = "A";
                            $pbx->save();
                        }
                    }
                }

            } else {
                if ($cust->balance >= $cust->email_low_balance_notification && $cust->balance >= 1) {

                    $pbxservi = PbxServices::where("status", "S")->where("customer_id", $cust->id)->get();
                    foreach ($pbxservi as $pbx) {
                        $val = Invoice::where("pbx_service_id", $pbx->id)->where("status", "!=", "DRAFT")->where("status", "!=", "SAVE_DRAFT")->where("status", "!=", "COMPLETED")->where("paid_status", "!=", "PAID")->whereNull('deleted_at')->get()->count();
                        if ($val == 0) {
                            //enviar correo
                            if ($pbx->suspension_type == "T") {
                                $controller = new PbxTenantSuspendService();
                                $response = $controller->unsuspend($pbx->id);
                            } else {
                                $controller = new PbxExtensionSuspendService();
                                $response = $controller->unsuspend($pbx->id);
                            }
                            $pbx->status = "A";
                            $pbx->save();
                        }
                    }
                }
            }
        }

        $customerlist = User::where("role", "customer")
            ->whereNull('users.deleted_at')
            ->where("users.status_customer", "A")
            ->where("users.balance", "<", 1)
            ->where("users.status_payment", "prepaid")
            ->get();

        foreach ($customerlist as $cust) {

            if ($cust->balance < 1) {

                $pbxservi = PbxServices::where("status", "A")->where("auto_suspension", 1)->where("customer_id", $cust->id)->get();

                foreach ($pbxservi as $pbx) {
                    $pbx->status = "S";
                    $pbx->save();
                    // obtener info del pbx package
                    $package = PbxPackages::findOrFail($pbx->pbx_package_id);
                    // incluir data
                    $pbx['pbx_package'] = $package;
                    // Enviar correo

                    if ($pbx->suspension_type == "T") {
                        $controller = new PbxTenantSuspendService();
                        $response = $controller->suspend($pbx->id);
                    } else {
                        $controller = new PbxExtensionSuspendService();
                        $response = $controller->suspend($pbx->id);
                    }


                    //Log::debug("correo de suspension suspendprepairservice");

                    $this->sendEmailToCustomer($pbx->customer_id, $pbx->company_id, 'suspend', 'Service Suspended', $pbx);




                }
            }
        }

        return Command::SUCCESS;
    }
}
