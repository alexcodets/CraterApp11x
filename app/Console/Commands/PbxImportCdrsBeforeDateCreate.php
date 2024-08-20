<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Crater\Models\Company;
use Crater\Models\PbxServices;
use Illuminate\Console\Command;
use Log;

class PbxImportCdrsBeforeDateCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:importCDRs:BeforeCreateService';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import CDR\'s for services with a start date prior to its creation date';

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

        //Log::debug("-------cdrs begores----------");

        foreach ($companies as $company) {
            $pbx_services = PbxServices::where('company_id', $company->id)
                ->whereNull('deleted_at')
                ->where('status', 'A')
                ->where('created_at', '>=', Carbon::now()->subDays(2)->format('Y-m-d H:i:s'))
                ->get();

            foreach ($pbx_services as $pbx_service) {
                $create_at = Carbon::createFromFormat('Y-m-d H:i:s', $pbx_service->created_at);
                $date_begin = Carbon::createFromFormat('Y-m-d H:i:s', Carbon::parse($pbx_service->date_begin)->format('Y-m-d H:i:s'));

                if ($create_at->gt($date_begin)) {
                    \Artisan::call('pbx:importCDRs', ['--start_date' => $date_begin, '--end_date' => $create_at, '--service' => $pbx_service->id]);
                }
            }
        }
    }
}
