<?php

namespace Crater\Traits;

use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServers;
use Crater\Models\PbxServices;
use Crater\Pbxware\PbxWareApi;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

trait PbxServiceValidationTrait
{
    /** @return Collection|PbxServices[] */
    public function getService(): Collection
    {

        if ($this->option('service') == null) {
            return PbxServices::whereNull('deleted_at')->where('status', 'A')->whereHas('pbxPackage', function ($q) {
                $q->where('call_ratings', 1);
            })->with(['tenant:id,code', 'pbxPackage', 'pbxPackage.server'])->get();
        }

        return PbxServices::whereNull('deleted_at')->where('status', 'A')->whereHas('pbxPackage', function ($q) {
            $q->where('call_ratings', 1);
        })->with(['tenant:id,code', 'pbxPackage', 'pbxPackage.server'])->find([$this->option('service')]);

    }

    /** @return Collection|PbxCdrTenant[] */
    public function getTenant(): \Illuminate\Support\Collection
    {
        if ($this->option('tenantid') === null) {
            return $this->validateTenants($this->tenantQuery()->get());
        }

        return $this->validateTenants($this->tenantQuery()->find([$this->option('tenantid')]));

    }

    private function tenantQuery(): Builder
    {
        return PbxCdrTenant::with('pbxServer');
        /*return PbxCdrTenant::where('status', '=', 1)
        ->whereHas('pbxServer', function ($query) {
            $query->where('status', '=', PbxServers::STATUS_ACTIVE)
                ->whereNull('deleted_at');
        })->with('pbxServer');*/

    }

    public function validateTenant($tenant): bool
    {
        return PbxServices::query()
            //->whereNotIn('status', ['P', 'C'])
            ->whereNull('deleted_at')
            ->whereHas('tenant', function ($query) use ($tenant) {
                return $query->where('code', $tenant->code)
                    ->where('tenantid', $tenant->tenantid)
                    ->where('pbx_server_id', $tenant->pbx_server_id);
            })->exists();

    }

    /* @var $tenants Collection|PbxCdrTenant[] */
    public function validateTenants(Collection $tenants): \Illuminate\Support\Collection
    {

        //Log::debug(__('PbxImportTenantCdr.log.validatiom.start'));
        if ($tenants->isEmpty()) {
            Log::debug(__('PbxImportTenantCdr.errors.service.empty'));
        }
        foreach ($tenants as $key => $tenant) {
            if (! $this->validateTenant($tenant)) {
                Log::debug(__('PbxImportTenantCdr.errors.tenant.invalid', ['id' => $tenant->id]));
                $this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.tenant.invalid', ['id' => $tenant->id]));
                $tenants->forget($key);
            }
            if (is_null($tenant->pbxServer)) {
                Log::debug(__('PbxImportTenantCdr.errors.server.null', ['id' => $tenant->id]));
                $this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.server.null', ['id' => $tenant->id]));
                $tenants->forget($key);
            }
            if ($tenant->pbxServer->deleted_at != null) {
                Log::debug(__('PbxImportTenantCdr.errors.server.deleted', ['id' => $tenant->id]));
                $this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.server.deleted', ['id' => $tenant->id]));
                $tenants->forget($key);
            }
            if ($tenant->pbxServer->status != PbxServers::STATUS_ACTIVE) {
                Log::debug(__('PbxImportTenantCdr.errors.server.deleted', ['id' => $tenant->id]));
                $this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.server.inactive', ['id' => $tenant->id]));
                $tenants->forget($key);
            }

            if ($tenant->job_active_at != null) {
                if ($tenant->job_active_at < now()->subDay()) {
                    //return User::where('updated_at', '<', now()->subDay()->startOfDay())->select('id','name')->get();
                    Log::debug(__('PbxImportTenantCdr.errors.tenant.active_old_job', ['id' => $tenant->id]));
                    $this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.tenant.active_old_job', ['id' => $tenant->id]));
                    //                    $tenant->job_active_at = null;
                    //                    $tenant->save();
                } else {
                    Log::debug(__('PbxImportTenantCdr.errors.tenant.active_job', ['id' => $tenant->id]));
                    $this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.tenant.active_job', ['id' => $tenant->id]));
                    $tenants->forget($key);
                }
            }

            if ($tenant->status == 0) {
                Log::debug(__('PbxImportTenantCdr.errors.tenant.disable', ['id' => $tenant->id]));
                //$this->notificationMail($tenant, $tenant->pbxServer, __('PbxImportTenantCdr.errors.tenant.disable', ['id' => $tenant->id]));
                $tenants->forget($key);
            }
        }

        if (isset($tenant) && $tenants->isEmpty()) {
            Log::debug(__('PbxImportTenantCdr.errors.tenant.empty_after'));
        }
        //Log::debug(__('PbxImportTenantCdr.log.validatiom.end'));

        return $tenants;
    }

    private function validateServices(Collection $pbxServices): bool
    {

        if ($pbxServices->isNotEmpty()) {
            return true;
        }

        if ($this->option('service') != null) {
            Log::debug(__('pbxImportCdrs.errors.service.not_found'));

            $this->error(__('pbxImportCdrs.errors.service.not_found'));

            return false;
        }
        $this->info(__('pbxImportCdrs.errors.service.empty'));
        Log::debug(__('pbxImportCdrs.errors.service.empty'));

        return false;

    }

    private function validateService(PbxPackages $pbxPackage): bool
    {
        $disabledService = $pbxPackage->call_ratings == 0 || $pbxPackage->status = ! 'A';
        $invalidRate = $pbxPackage->rate_per_minutes == null;
        $invalidData = $pbxPackage->minutes_increments == null;
        $noActiveServer = $pbxPackage->pbxServer->status != 'A';

        if ($disabledService || $invalidRate || $invalidData || $noActiveServer) {
            if ($this->option('service') != null) {
                $this->validateSpecifiedService($pbxPackage);
            }

            return false;
        }

        return true;
    }

    private function validateSpecifiedService(PbxPackages $pbxPackage)
    {
        if ($pbxPackage->call_ratings == 0 or $pbxPackage->call_ratings == null) {
            $this->error(__('pbxImportCdrs.errors.service.call_rating'));
            Log::debug(__('pbxImportCdrs.errors.service.call_rating'));

        }
        if ($pbxPackage->status != 'A') {
            $this->error(__('pbxImportCdrs.errors.service.status'));
            Log::debug(__('pbxImportCdrs.errors.service.status'));

        }
        if ($pbxPackage->rate_per_minutes == null) {
            $this->error(__('pbxImportCdrs.errors.service.rate_per_minutes'));
            Log::debug(__('pbxImportCdrs.errors.service.rate_per_minutes'));

        }
        if ($pbxPackage->minutes_increments == 0) {
            $this->error(__('pbxImportCdrs.errors.service.minutes_increments'));
            Log::debug(__('pbxImportCdrs.errors.service.minutes_increments'));

        }
        if ($pbxPackage->pbxServer->status != 'A') {
            $this->error(__('Pbx server is inactive'));
            Log::debug(__('Pbx server is inactive'));

        }
    }

    public function validateDates($start, $end): bool
    {
        if ($start->greaterThan($end)) {
            //Log::debug("Start date ({$start->format('Y-m-d H:i:s')}) cannot be Greater than End Date ({$end->format('Y-m-d H:i:s')})");
            $this->error(__('pbxImportCdrs.errors.date.start_greater', ['start' => $start->format('Y-m-d H:i:s'), 'end' => $end->format('Y-m-d H:i:s')]));
            Log::debug(__('pbxImportCdrs.errors.date.start_greater', ['start' => $start->format('Y-m-d H:i:s'), 'end' => $end->format('Y-m-d H:i:s')]));

            return true;
        }

        return false;
    }

    public function validateApiTenant(PbxWareApi $api, string $code): array
    {
        $response = $api->tenantConfiguration($code);

        if (! $response['success']) {
            $this->error(__('pbxImportCdrs.errors.api.tenant_id'));

            return [
                'success' => false,
                'message' => __('pbxImportCdrs.errors.api.tenant_id'),
                'subject' => 'Error with Tenant ID',
                'type' => 'tenant_id',
            ];
        }

        return ['success' => true];

    }

    /**
     * @throws Exception
     */
    public function validateApiService(PbxWareApi $api, PbxServices $pbxServices): array
    {
        $response = $api->checkConnection();
        $response = $pbxServices->pbxPackage->pbxServer->status == PbxServers::STATUS_ACTIVE;

        if (! $response) {
            $this->error(__('pbxImportCdrs.errors.api.connection'));

            return [
                'success' => false,
                'message' => __('pbxImportCdrs.errors.api.connection'),
                'subject' => 'Error with api Connection',
                'type' => 'connection',
            ];
        }

        return $this->validateApiTenant($api, $pbxServices->tenant->code);

    }
}
