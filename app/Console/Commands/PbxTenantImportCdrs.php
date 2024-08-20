<?php

namespace Crater\Console\Commands;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Crater\Helpers\General;
use Crater\Jobs\PbxImportTenantCdrJob;
use Crater\Mail\PbxTenantImportCdrMail;
use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxJobLog;
use Crater\Models\PbxServers;
use Crater\Models\PbxServices;
use Crater\Models\PbxTenantCdr;
use Crater\Pbxware\PbxWareApi;
use Crater\Traits\PbxServiceValidationTrait;
use DateTime;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Throwable;

class PbxTenantImportCdrs extends Command
{
    use PbxServiceValidationTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // {--tenantid= : tenantid, if not Tenantid is inputed them it will run for every Tenant, dont confuse tenantid with tenant_id.}
    protected $signature = 'pbx:TenantImportCDRs
                            {--tenantid= : id for the PbxCdrTenant, if not pbxTenant is inputed them it will run for every Tenant.}
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
    protected $description = 'Command to import CDR\'s ( Calls ) from a tenant, that could be in one or several services';

    public $start;

    public $end;
    public const STATUS = [8, 4, 2, 1];

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
     * @throws Throwable
     */
    public function handle(): int
    {
        Log::debug('--------------------------------- inicio Tenant import');
        Log::debug(trans('PbxImportTenantCdr.log.block.start'));
        Log::debug(trans('PbxImportTenantCdr.log.command.start'));
        Log::debug(trans('PbxImportTenantCdr.log.block.end'));

        if ($this->validateInputs()) {
            Log::debug(trans('PbxImportTenantCdr.log.block.start'));
            Log::debug(trans('PbxImportTenantCdr.log.command.end'));
            Log::debug(trans('PbxImportTenantCdr.log.block.end'));

            return self::SUCCESS;
        }

        $this->generateTenants();

        $tenants = $this->getTenant();

        if ($tenants->isEmpty()) {
            $this->info('empty');
            Log::debug(trans('PbxImportTenantCdr.log.block.start'));
            Log::debug(trans('PbxImportTenantCdr.log.command.end'));
            Log::debug(trans('PbxImportTenantCdr.log.block.end'));

            return true;
        }

        Log::debug(trans('PbxImportTenantCdr.log.success.validation'));

        $this->end = $this->setEndDate();
        $this->setStartDate();
        //$n = 0;

        Log::debug(trans('PbxImportTenantCdr.log.tenant.start'));

        foreach ($tenants as $tenant) {
            /* @var PbxCdrTenant $tenant */
            $timeZoneNow = $tenant->pbxServer->timezone_key;
            if (! $this->isValidTimezone($timeZoneNow)) {
                $this->error(trans('pbxImportCdrs.errors.service.timezone'));
                Log::debug(trans('pbxImportCdrs.errors.service.timezone'));
                $this->notificationMail($tenant, $tenant->pbxServer, __('pbxImportCdrs.errors.service.timezone'));

                continue;
            }

            if (! $this->validateServer($tenant->pbxServer)) {
                Log::debug('The server is down');
                $this->notificationMail($tenant, $tenant->pbxServer, 'The server is down');

                continue;
            }

            $api = new PbxWareApi($tenant->pbxServer);
            if (! $this->validateApiTenant($api, $tenant->tenantid)['success']) {
                Log::debug($tenant->tenantid);
                Log::debug(__('pbxImportCdrs.errors.api.tenant_id'));
                $this->notificationMail($tenant, $tenant->pbxServer, __('pbxImportCdrs.errors.api.tenant_id'));

                continue;
            }

            // TODO: Se deberia validar el servicio y/o el paquete sobre el que esta esto, y cuales serian
            // dichas validaciones.

            //$cdr = new PbxTenantCdr();
            //  Em que momento inicia el tiempo del Tenant a observar?, lo colocaré como desde la fecha en la que se creo.
            $serviceStartDate = $this->startDate($tenant);

            if ($this->validateDates($serviceStartDate, $this->end)) {
                Log::debug(__('pbxImportCdrs.errors.date.start_greater'));
                $this->notificationMail(
                    $tenant,
                    $tenant->pbxServer,
                    __(
                        'pbxImportCdrs.errors.date.start_greater',
                        [
                            'start' => $serviceStartDate->format('Y-m-d H:i:s'), 'end' => $this->end->format('Y-m-d H:i:s')
                        ]
                    )
                );

                continue;
            }

            $this->info('----------------------------Database Time----------------------------');

            //$this->importCdr($api, $pbxService, $service, $start, $this->end, $cdr);
            $dates = $this->getDateValues($serviceStartDate, $timeZoneNow);
            $statuses = $this->getCdrStatus();
            Log::debug('----------------------------Jobs Start----------------------------');

            $tenant->last_update = $dates[0]['end'];
            $tenant->job_active_at = now();
            $tenant->save();

            $jobs = [];
            foreach ($dates as $date) {
                $date['start']->tz = $timeZoneNow;
                $date['end']->tz = $timeZoneNow;
                //$i++;
                Log::debug("TimeLapse: {$date['start']->format('Y-m-d H:i:s')} {$date['end']->format('Y-m-d H:i:s')}, for Status: {$statuses->implode(',')}");
                $jobs[] = new PbxImportTenantCdrJob($tenant, $api, $date['start'], $date['end'], $statuses->implode(','));
            }

            Log::debug(trans('PbxImportTenantCdr.log.batch.start'));
            $batch = Bus::batch($jobs)->then(function (Batch $batch) use ($tenant) {
                PbxJobLog::create([
                    'name' => 'PbxTenantImportCdrJob',
                    'response' => 'Process finished successfully.',
                    'lvl' => PbxJobLog::LVL_INFO,
                    'data' => json_encode(['success' => true]),
                    'pbx_service_id' => null,
                ]);
            })->catch(function (Batch $batch, Throwable $e) {
                Log::debug('Catch');
                PbxJobLog::create([
                    'name' => 'PbxTenantImportCdrJob',
                    'response' => 'Process Exit with error.',
                    'lvl' => PbxJobLog::LVL_ERROR,
                    'data' => json_encode($e->getMessage()),
                    'pbx_service_id' => null,
                ]);
            })->finally(function (Batch $batch) use ($tenant) {
                Log::debug('Finally');
                $tenant->refresh();
                $tenant->job_active_at = null;
                $tenant->save();
            })->onQueue('pbxTenantCdrImport')->name("Import TenantCdr for Tenant: {$tenant->id}")->dispatch();

        }

        Log::debug(trans('PbxImportTenantCdr.log.tenant.end'));

        Log::debug(trans('PbxImportTenantCdr.log.block.start'));
        Log::debug(trans('PbxImportTenantCdr.log.command.end'));
        Log::debug(trans('PbxImportTenantCdr.log.block.end'));

        Log::debug('--------------------------------- fin Tenant import');

        return self::SUCCESS;
    }

    public function isValidNumber($value): bool
    {
        if (is_numeric($value) && is_int(intval($value))) {
            return true;
        }

        return false;
    }

    public function validateInputs(): bool
    {
        $error = false;
        if ($this->option('tenantid') != null && ! $this->isValidNumber($this->option('tenantid'))) {
            $error = true;
            $text = trans('PbxImportTenantCdr.errors.service_int', ['id' => $this->option('tenantid')]);
            $this->error($text);
            Log::debug($text);
        }
        if (! $this->isValidNumber($this->option('days'))) {
            $error = true;
            $text = trans('pbxImportCdrs.errors.days_int');
            $this->error($text);
            Log::debug($text);
        }
        if (! $this->isValidNumber($this->option('hours'))) {
            $error = true;
            $text = trans('pbxImportCdrs.errors.hours_int');
            $this->error($text);
            Log::debug($text);
        }
        if (! $this->isValidNumber($this->option('minutes'))) {
            $error = true;
            $text = trans('pbxImportCdrs.errors.minutes_int');
            $this->error($text);
            Log::debug($text);
        }
        if ($this->option('start_date')) {
            if (! $this->isValidDate($this->option('start_date'))) {
                $error = true;
                $text = trans('pbxImportCdrs.errors.start_date');
                $this->error($text);
                Log::debug($text);
            }
        }
        if ($this->option('end_date')) {
            if (! $this->isValidDate($this->option('end_date'))) {
                $error = true;
                $text = trans('pbxImportCdrs.errors.end_date');
                $this->error($text);
                Log::debug($text);
            }
        }

        return $error;
    }

    /**
     * Set the corresponding Start Date for each Service
     *
     * @param PbxCdrTenant $tenant
     * @return Carbon
     */
    private function startDate(PbxCdrTenant $tenant): Carbon
    {
        $dateBegin = $tenant->last_update ? $tenant->last_update : ($tenant->date_begin ? $tenant->date_begin->startOfDay() : now()->startOfDay());

        if ($this->option('days') > 0 || $this->option('hours') > 0 || $this->option('minutes') > 0) {
            return $this->start;
        }

        if ($dateBegin->greaterThan($this->start)) {
            return $dateBegin;
        }

        if ($this->option('start_date')) {
            return $this->start;
        }
        // TODO: Analizar de nuevo el orden.
        $cdr = PbxTenantCdr::where('pbx_cdr_tenant_id', $tenant->id)->orderBy('start_date', 'desc')->first();
        if ($cdr == null) {
            return $dateBegin;
        }

        return $dateBegin;
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

    public function setEndDate()
    {
        if ($this->option('end_date')) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->option('end_date'), 'UTC (UTC)');
        }

        return now('UTC');
    }

    public function getDateValues(Carbon $start, ?string $timeZone = null): array
    {
        now()->format('Y-m-d H:i:s');
        Log::debug("Get Range, Start Time: {$start->format('Y-m-d H:i:s')} ");
        $start->tz = $timeZone;
        $this->end->tz = $timeZone;
        $dates = [];
        $lapse = '3';

        if ($start->diffInDays($this->end) > 2) {
            $lapse = '8';
        }

        $period = CarbonPeriod::create($start->format('Y-m-d H:i:s'), "{$lapse} hour", $this->end->format('Y-m-d H:i:s'));
        foreach ($period as $date) {
            $dates[] = [
                'start' => Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d H:i:s'), $timeZone),
                'end' => Carbon::createFromFormat('Y-m-d H:i:s', $date->copy()->addHours($lapse)->format('Y-m-d H:i:s'), $timeZone),
            ];
        }

        Log::debug("Range start now at: {$dates[0]['start']->format('Y-m-d H:i:s')} ");
        $dates = array_reverse($dates);
        $dates[0]['end'] = $this->end;

        if ($this->end->equalTo($dates[0]['start'])) {
            array_shift($dates);
        }

        return $dates;

    }

    public function isValidDate(string $date, string $format = 'Y-m-d H:i:s'): bool
    {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) == $date;
    }

    public function isValidTimezone($timezone = null): bool
    {
        return in_array($timezone, timezone_identifiers_list());
    }

    // All the status are returned.
    public function getCdrStatus(): Collection
    {
        return collect(self::STATUS);
    }

    // WIP: TODO: Logic para eliminar tenant si no hay ningún servicio con dicha combination.

    /**
     * @throws Exception
     */
    public function deleteTenant()
    {
        throw new Exception('Method not implemented');
    }

    public function generateTenants(): void
    {
        // Check PbxTenantCdr for
        $now = now()->format('Y-m-d');
        $items = PbxServices::query()
            ->whereNotIn('pbx_services.status', ['P', 'C'])
            ->join('pbx_packages', 'pbx_packages.id', '=', 'pbx_services.pbx_package_id')
            ->join('pbx_tenant', 'pbx_services.pbx_tenant_id', 'pbx_tenant.id')
            ->get(['pbx_packages.pbx_server_id as pbx_server_id', 'pbx_tenant.code', 'pbx_tenant.tenantid', 'pbx_services.date_begin']);

        if ($items->isEmpty()) {
            return;
        }

        foreach ($items as $tenant) {

            Log::debug($tenant);

            $pbxCdrTenant = PbxCdrTenant::firstOrCreate(
                [
                'code' => $tenant->code,
                'pbx_server_id' => $tenant->pbx_server_id,
                'tenantid' => $tenant->tenantid,
            ],
                [
                    'date_begin' => $tenant->date_begin ?? $now,
                    'status' => 0,
                ]
            );

            if ($pbxCdrTenant->date_begin > $tenant->date_begin) {
                $pbxCdrTenant->date_begin = $tenant->date_begin;
                $pbxCdrTenant->save();
            }

        }

        PbxServices::query()->whereNotIn('pbx_services.status', ['P', 'C'])->join('pbx_packages', 'pbx_packages.id', '=', 'pbx_services.pbx_package_id')->join('pbx_tenant', 'pbx_services.pbx_tenant_id', 'pbx_tenant.id')->get(['pbx_packages.pbx_server_id as pbx_server_id', 'pbx_tenant.code', 'pbx_tenant.tenantid', 'pbx_services.date_begin']);

    }

    public function validateServer(PbxServers $pbxServer): bool
    {
        return $pbxServer->status == PbxServers::STATUS_ACTIVE;
    }

    public function notificationMail(PbxCdrTenant $tenant, ?PbxServers $pbxServers = null, string $extraBody = '')
    {

        $lock = Cache::lock("TenantImportCdr_id:{$tenant->id}", 60 * 60 * 24);
        if ($lock->get()) {

            $users = General::getPbxNotificationAdmins();

            foreach ($users as $user) {
                try {
                    Mail::to($user->email)->send(new PbxTenantImportCdrMail($tenant, $extraBody));
                } catch (Throwable $th) {
                    Log::debug('Error sending mail for PbxTenantImportCdrs');
                    Log::debug($th->getMessage());
                }
            }

            if ($pbxServers) {
                $check = $pbxServers->company->server_check_notification;

                try {
                    Mail::to($check['server_email'])->send(new PbxTenantImportCdrMail($tenant, $extraBody));
                } catch (Throwable $th) {
                    Log::debug('Error sending mail for PbxTenantImportCdrs');
                    Log::debug($th->getMessage());
                }
            }
        }

    }
}
