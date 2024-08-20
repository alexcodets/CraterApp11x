<?php

namespace Crater\Console\Commands;

use Cache;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Crater\Jobs\PbxImportCdrJob;
use Crater\Mail\SimpleMail;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxJobLog;
use Crater\Models\PbxServers;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Crater\Pbxware\PbxWareApi;
use Crater\Pbxware\Service\PbxWareService;
use Crater\Traits\PbxServiceValidationTrait;
use DateTime;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use Mail;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Throwable;

class PbxImportCdrs extends Command
{
    use PbxServiceValidationTrait;

    public Carbon $start;

    public Carbon $end;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:importCDRs
                            {--service= : pbxService id, if not Service id is inputted them it will run for every PbxService.}
                            {--minutes=0 : how many minutes should the import go back.}
                            {--hours=0 : how many hours should the import go back.}
                            {--days=0 : how many days should the import go back.}
                            {--start_date= : string, Starting date using format "Y-m-d H:i:s" with UTC timezone.}
                            {--end_date= : string, Ending date using format "Y-m-d H:i:s" with UTC timezone.}
                            {--user= : user id, used to run every service for a specific user TBA.}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to import CDR\'s ( Calls ) from some tenant and from some PBXServer';

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
     * @throws Throwable
     */
    public function handle(): int
    {
        Log::debug("-------ejecuto cron pbx antes validateInputs----------");

        if ($this->validateInputs()) {
            return self::FAILURE;
        }

        $services = $this->getService();

        //Log::debug("-------ejecuto cron pbx antes validateServices----------");
        if (! $this->validateServices($services)) {
            return self::FAILURE;
        }

        if ($this->option('hours')) {
            Log::debug('Hours value: '.$this->option('hours'));
        }

        $this->info(trans('pbxImportCdrs.process.start'));
        $this->end = $this->setEndDate();
        $service = new PbxWareService();

        $this->setStartDate();
        $n = 0;
        $totalJobs = 0;
        $tempCount = 0;
        Log::debug("----------------------------PbxImportCdrs Start----------------------------");

        foreach ($services as $pbxService) {
            $this->info("----------------");
            $timeZoneNow = $pbxService->pbxPackage->pbxServer->timezone_key;

            $this->info("-------now----------");
            if (! $this->isValidTimezone($timeZoneNow)) {
                $this->error(trans('pbxImportCdrs.errors.service.timezone'));
                Log::debug(trans('pbxImportCdrs.errors.service.timezone'));

                continue;
            }
            //Log::debug(now($timeZoneNow));
            $this->info(now($timeZoneNow));
            $this->info("-------End----------");
            $this->info($this->end);
            $this->info("-------Start----------");
            $this->info($this->start);

            $api = new PbxWareApi($pbxService->pbxPackage->pbxServer);
            //Log::debug("ini: " . $pbxService->pbx_services_number);

            //Log::debug($pbxService->pbxPackage);
            if (! $this->validateService($pbxService->pbxPackage)) {
                continue;
            }
            $tempResponse = $this->validateLocalApiService($api, $pbxService);

            if (! $tempResponse['success']) {
                $this->error($tempResponse['message']);
                Log::debug('Error while validating the Api');
                Log::debug($tempResponse['message']);
                $lock = Cache::lock($tempResponse['type']."_{$pbxService->id}", 60 * 60 * 24);

                if ($lock->get()) {
                    try {
                        $mails = $pbxService->company->settings()->where('option', '=', 'server_notification')->pluck('value');
                        $mails->merge(
                            User::where('role', '=', 'super admin')
                                ->where('pbx_notification', '=', 1)
                                ->pluck('email')
                        );

                        foreach ($mails as $mail) {
                            Mail::to($mail)->send(new SimpleMail($tempResponse['subject'], $tempResponse['description'], $pbxService->company));
                        }

                    } catch (Throwable $th) {
                        Log::error('Error while sending email for PbxImportCdrs');
                        Log::error($th->getMessage());
                        Log::error($th->getTraceAsString());
                    }
                }

                continue;
            }
            Log::debug("ini: after pbxPackage");

            $cdr = new CallDetailRegister();
            $cdr->setTable($cdr->firstOrCreateTableFromService($pbxService));
            $serviceStartDate = $this->startDate($cdr, $pbxService);

            if ($this->validateDates($serviceStartDate, $this->end)) {
                continue;
            }
            $this->info('----------------------------Database Time----------------------------');

            //$this->importCdr($api, $pbxService, $service, $start, $this->end, $cdr);
            $dates = $this->getDateValues($serviceStartDate, $timeZoneNow);
            $pbxService->is_importing = true;
            $pbxService->first_time_import = false;
            $pbxService->save();
            $statuses = $this->getCdrStatus($pbxService);
            Log::debug('-------------');
            Log::debug("  Creating Jobs for PbxServiceId: {$pbxService->id}");
            Log::debug("  Starting date: {$serviceStartDate->format('Y-m-d H:i:s')}");
            Log::debug("  Time zone for service {$pbxService->id} is : {$timeZoneNow}");
            Log::debug('  How many Dates: '.count($dates));
            Log::debug('  How many Status: '.count($statuses));


            Log::debug("----------------------------Jobs Start----------------------------");
            $jobs = [];
            foreach ($dates as $date) {
                $date['start']->tz = $timeZoneNow;
                $date['end']->tz = $timeZoneNow;
                $jobs[] = new PbxImportCdrJob($api, $pbxService, $service, $date['start'], $date['end'], $statuses->implode(','));

                /*                foreach ($statuses as $status) {
                                    //Log::debug("TimeLapse: {$date['start']->format('Y-m-d H:i:s')} {$date['end']->format('Y-m-d H:i:s')}, for Status: {$status}");
                                    $jobs[] = new PbxImportCdrJob($api, $pbxService, $service, $date['start'], $date['end'], $status);
                                }*/
            }

            if (! $jobs) {
                continue;
            }
            $tempCount = count($jobs);
            Log::debug('Before PbxImportCdrs batch');
            $batch = Bus::batch($jobs)->then(function (Batch $batch) use ($pbxService) {
                //Log::debug("Then");
                // All jobs completed successfully...
                PbxJobLog::create([
                    'name' => 'PbxImportCdrJob',
                    'response' => "Process finished successfully.",
                    'lvl' => PbxJobLog::LVL_INFO,
                    'data' => json_encode(['success' => true]),
                    'pbx_service_id' => $pbxService->id,
                ]);
            })->catch(function (Batch $batch, Throwable $e) use ($pbxService) {
                // First batch job failure detected...
                //Log::debug("Catch");
                PbxJobLog::create([
                    'name' => 'PbxImportCdrJob',
                    'response' => "Process Exit with error.",
                    'lvl' => PbxJobLog::LVL_ERROR,
                    'data' => json_encode($e->getMessage()),
                    'pbx_service_id' => $pbxService->id,
                ]);
            })->finally(function (Batch $batch) use ($pbxService) {
                // The batch has finished executing...
                //Log::debug("Finally");
                $pbxService->is_importing = false;
                $pbxService->save();
            })->name("Import Cdr for Service: {$pbxService->id}")->dispatch();

            //Here I get the id to cancel or check
            //return $batch->id;

            $totalJobs += $tempCount;
            Log::debug("  Creating {$tempCount} jobs for service {$pbxService->id}");

            //}
            //Log::debug("-----------------------------Jobs End-----------------------------");
            dispatch(function () use ($pbxService) {
                $pbxService->is_importing = false;
                $pbxService->save();
            });
            $n++;

        }

        if ($n > 0) {
            Log::debug("---------------Totals------------------");
            Log::debug("    Services with Job Generated : {$n}");
            Log::debug("    Total Jobs Generated : {$totalJobs}");
            Log::debug("---------------Totals------------------");
            $this->info(trans('pbxImportCdrs.calculate.totals', ['total' => $n]));
        }

        Log::debug("----------------------------PbxImportCdrs End-----------------------------");

        return self::SUCCESS;

    }

    public function validateInputs(): bool
    {
        $error = false;
        if ($this->option('service') != null && ! $this->isValidNumber($this->option('service'))) {
            $error = true;
            $this->error(trans('pbxImportCdrs.errors.service_int'));
        }
        if (! $this->isValidNumber($this->option('days'))) {
            $error = true;
            $this->error(trans('pbxImportCdrs.errors.days_int'));
        }
        if (! $this->isValidNumber($this->option('hours'))) {
            $error = true;
            $this->error(trans('pbxImportCdrs.errors.hours_int'));
        }
        if (! $this->isValidNumber($this->option('minutes'))) {
            $error = true;
            $this->error(trans('pbxImportCdrs.errors.minutes_int'));
        }
        if ($this->option('start_date')) {
            if (! $this->isValidDate($this->option('start_date'))) {
                $error = true;
                $this->error(trans('pbxImportCdrs.errors.start_date'));
            }
        }
        if ($this->option('end_date')) {
            if (! $this->isValidDate($this->option('end_date'))) {
                $error = true;
                $this->error(trans('pbxImportCdrs.errors.end_date'));
            }
        }

        return $error;
    }

    public function isValidNumber($value): bool
    {
        if (is_numeric($value) && is_int(intval($value))) {
            return true;
        }

        return false;
    }

    public function isValidDate(string $date, string $format = "Y-m-d H:i:s"): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    public function validateLocalApiService(PbxWareApi $api, PbxServices $pbxServices): array
    {
        $pbx_services_number = $pbxServices->pbx_services_number;
        $pbx_package_name = $pbxServices->pbxPackage->pbx_package_name;
        $server_name = $pbxServices->pbxPackage->pbxServer->server_label;
        // Tenant
        $tenantCode = $pbxServices->tenant->code;

        $description = '<p>A error has been found while attempting to import CDRs.<p>'.
            '<p>Pbx Service Number: <b>'.$pbx_services_number.'<b><p>'.
            '<p>Pbx Package Name: <b>'.$pbx_package_name.'<b><p>'.
            '<p>Server Name: <b>'.$server_name.'<b><p>'.
            '<p>Tenant Code: <b>'.$tenantCode.'<b><p>';

        $status = $pbxServices->pbxPackage->pbxServer->status;

        if (is_null($status)) {
            return [
                'success' => false,
                'message' => trans('pbxImportCdrs.errors.api.connection_null'),
                'subject' => 'Error with Api Connection',
                'description' => $description.trans('pbxImportCdrs.errors.api.connection_null'),
                'type' => 'connection_null',
            ];
        }

        if ($status == PbxServers::STATUS_INACTIVE) {
            $this->error(trans('pbxImportCdrs.errors.api.connection'));

            return [
                'success' => false,
                'message' => trans('pbxImportCdrs.errors.api.connection'),
                'subject' => 'Error with Api Connection',
                'description' => $description.trans('pbxImportCdrs.errors.api.connection'),
                'type' => 'error_connection',
            ];
        }

        if (is_null($tenantCode)) {
            return [
                'success' => false,
                'message' => trans('pbxImportCdrs.errors.api.tenant_code_null'),
                'subject' => 'Error with Tenant ID',
                'description' => $description.trans('pbxImportCdrs.errors.api.tenant_code_null'),
                'type' => 'tenant_code_null',
            ];
        }

        $response = $api->tenantConfiguration($tenantCode);

        if (! $response['success']) {
            $this->error(trans('pbxImportCdrs.errors.api.tenant_id'));

            return [
                'success' => false,
                'message' => trans('pbxImportCdrs.errors.api.tenant_id'),
                'subject' => 'Error with Tenant ID',
                'description' => $description.trans('pbxImportCdrs.errors.api.tenant_id'),
                'type' => 'invalid_tenant_id',
            ];
        }

        return ['success' => true];

    }

    public function setEndDate()
    {
        if ($this->option('end_date')) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->option('end_date'), 'UTC (UTC)');
        }

        return now('UTC');
    }

    /**
     *  Initialize the start date
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function setStartDate(): void
    {
        if ($this->option('start_date')) {
            $this->start = Carbon::createFromFormat('Y-m-d H:i:s', $this->option('start_date'), 'UTC (UTC)');
            $this->info('----------Time----------');
            $this->info($this->start);
            $this->info('----------Time----------');

            return;
        }
        if ($this->option('days') > 0 || $this->option('hours') > 0 || $this->option('minutes') > 0) {
            $this->info('----------Set Time----------');

            $start = now('UTC (UTC)');
            $start->subDays($this->option('days'));
            $start->subHours($this->option('hours'));
            $start->subMinutes($this->option('minutes'));
            $this->start = $start;

            return;
        }

        $this->start = now('UTC (UTC)');
    }

    public function isValidTimezone($timezone = null): bool
    {
        return in_array($timezone, timezone_identifiers_list());
    }

    /**
     * Set the corresponding Start Date for each Service
     *
     * @throws InvalidArgumentException
     */
    private function startDate(CallDetailRegister $cdr, PbxServices $pbxService): Carbon
    {
        $dateBegin = Carbon::createFromFormat('Y-m-d', $pbxService->date_begin, $pbxService->pbxPackage->pbxServer->timezone_key)->startOfDay();

        if ($dateBegin->greaterThan($this->start)) {
            return $dateBegin;
        }

        if ($this->option('start_date')) {
            return $this->start;
        }

        $cdr = $cdr->orderBy('start_date', 'desc')->first();
        //$pbxService->jobLogs()->count();
        if ($cdr == null && $pbxService->first_time_import) {
            return $dateBegin;
        }

        if ($this->option('days') > 0 || $this->option('hours') > 0 || $this->option('minutes') > 0) {
            return $this->start;
        }

        return $dateBegin;
    }

    public function getDateValues(Carbon $start, string $timeZone = null): array
    {
        //now()->format('Y-m-d H:i:s')
        //Log::debug("Get Range, Start Time: {$start->format('Y-m-d H:i:s')} ");
        $start->tz = $timeZone;
        $this->end->tz = $timeZone;
        $dates = [];
        $lapse = '3';

        if ($start->diffInDays($this->end) > 2) {
            $lapse = '12';
        }

        $period = CarbonPeriod::create($start->format('Y-m-d H:i:s'), "{$lapse} hour", $this->end->format('Y-m-d H:i:s'));
        foreach ($period as $date) {
            $dates[] = [
                'start' => Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d H:i:s'), $timeZone),
                'end' => Carbon::createFromFormat('Y-m-d H:i:s', $date->copy()->addHours($lapse)->format('Y-m-d H:i:s'), $timeZone),
            ];
        }

        //Log::debug("Range start now at: {$dates[0]['start']->format('Y-m-d H:i:s')} ");
        $dates = array_reverse($dates);
        $dates[0]['end'] = $this->end;

        if ($this->end->equalTo($dates[0]['start'])) {
            array_shift($dates);
        }

        return $dates;

    }

    public function getCdrStatus(PbxServices $pbxService): Collection
    {
        /** @var Collection $status */
        $status = $pbxService->pbxPackage->status_array;

        if ($status->isNotEmpty()) {
            return $status;
        }

        return $pbxService->pbxPackage->pbxServer->status_array;

    }

    private function importCdr(PbxWareApi $api, PbxServices $pbxService, PbxWareService $service, Carbon $start, Carbon $end, CallDetailRegister $cdr)
    {

        $response = $service->importCdrsByTrunk($api, $pbxService, $start, $end, $cdr);

        if (! $response['success']) {
            //Log::debug('-----Error-----');
            $this->error('-----Error-----');
            $this->error(trans('pbxImportCdrs.errors.pbx_service', ['id' => $pbxService->id]));
            //Log::debug($response['message']);
            $this->error($response['message']);

            $this->error('---------------');
        }
        if ($response['success']) {
            $this->info('Process Finished Successfully');
            //Log::debug('Process Finished Successfully');
        }
        if (isset($response['time'])) {
            $this->info('----------Time----------');

            $this->info('Main process: '.$response['time']['main']);

            $this->info('getting cdr from api: '.$response['time']['cdrs']);

            if (isset($response['time']['trunks'])) {
                $this->info('Storing Cdrs into Database: '.$response['time']['trunks']);
            }
        }

    }
}
