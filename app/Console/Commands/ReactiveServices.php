<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerPackage;
use Crater\Models\Invoice;
use Illuminate\Console\Command;

class ReactiveServices extends Command
{
    public const allow_unsuspend_packages_job = 'allow_unsuspend_packages_job';
    public const period_run_unsuspend_packages_job = 'period_run_unsuspend_packages_job';
    public const time_unsuspend_packages_job = 'time_unsuspend_packages_job';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reactive:Services';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reactived suspended services';

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

        $companies = Company::get();

        foreach ($companies as $company) {

            $settings = CompanySetting::whereCompany($company->id)
                ->whereIn('option', [
                    self::allow_unsuspend_packages_job,
                    self::period_run_unsuspend_packages_job,
                    self::time_unsuspend_packages_job,
                ])
                ->pluck('value', 'option');

            if (isset($settings[self::allow_unsuspend_packages_job]) && $settings[self::period_run_unsuspend_packages_job]) {
                if (isset($settings[self::time_unsuspend_packages_job])) {

                    $last_time = Carbon::createFromFormat('Y-m-d H:i', $settings[self::time_unsuspend_packages_job]);
                    $next_time = $last_time->copy()->addMinutes($settings[self::period_run_unsuspend_packages_job])->format('Y-m-d H:i');
                    $now = Carbon::now()->format('Y-m-d H:i');
                    $result = Carbon::now()->diffInMinutes($last_time);
                    self::unsuspends($company->id);
                    if ($result >= intval($settings[self::period_run_unsuspend_packages_job])) {
                        CompanySetting::setSettings([self::time_unsuspend_packages_job => Carbon::now()->format('Y-m-d H:i')], $company->id);

                        self::unsuspends($company->id);
                    }

                } else {

                    CompanySetting::setSettings([self::time_unsuspend_packages_job => Carbon::now()->format('Y-m-d H:i')], $company->id);

                }

            }

        }

        return 0;
    }

    public static function unsuspends($company)
    {

        $service = CustomerPackage::where("company_id", $company)->where("status", "S")->get();


        foreach($service as $ap) {
            $val = Invoice::where("customer_packages_id", $ap->id)->where("status", "!=", "DRAFT")->where("status", "!=", "SAVE_DRAFT")->where("status", "!=", "COMPLETED")->where("paid_status", "!=", "PAID")->whereNull('deleted_at')->get()->count();

            if($val == 0) {
                //enviar correo
                $ap->status = "A";
                $ap->save();
            }
        }


    }
}
