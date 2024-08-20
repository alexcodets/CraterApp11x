<?php

namespace Crater\Jobs;

use Crater\Models\PbxExtensions;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServicesExtensions;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Log;

class PbxServerTenantExtensionSynchronizeJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use Batchable;

    private int $tenant_id;

    public function __construct(int $tenant_id)
    {
        $this->tenant_id = $tenant_id;
        Log::withContext([
            'tenant_id' => $this->tenant_id,
            'Module' => 'PbxServerTenantExtensionSynchronizeJob',
        ]);
    }

    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }
        Log::debug('Process Start');
        $tenant = PbxServerTenant::find($this->tenant_id);
        if (is_null($tenant)) {
            Log::error('Could not find server tenant for this job.');

            return;
        }
        if (is_null($tenant->pbxServer)) {
            Log::error('Could not find pbxServer for the tenant from this job.');

            return;
        }

        $api = new PbxWareApi($tenant->pbxServer);
        $extension = $api->extensionsList($tenant->tenant_id);

        if (! $extension['success']) {
            Log::error('Could not get extension list from pbx server');
            Log::error($extension['message']);

            return;
        }

        $extension = $extension['data'];

        $apiExtIds = [];

        foreach ($extension as $index => $item) {
            $details = $api->extensionConfiguration($tenant->tenant_id, $index);
            if (! $details['success']) {
                Log::error('Could not get extension details from pbx server', ['extension_api_id' => $index]);
                Log::error($details['message']);

                continue;
            }

            $details = $details['data'][$index];

            $apiExtIds[] = $index;
            $this->createOrUpdateExt($tenant, $index, $item, $details);

        }

        $serverDidIds = PbxExtensions::query()
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->pluck('api_id')->toArray();

        $toDelete = array_diff($serverDidIds, $apiExtIds);

        if ($toDelete) {
            $this->deleteDid($toDelete, $tenant);
        }
        Log::debug('Process End');

    }

    public function createOrUpdateExt(PbxServerTenant $tenant, int $index, array $item, array $details): PbxExtensions
    {
        $ext = PbxExtensions::query()
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('api_id', $index)
            ->first();

        $ext = $ext ?: new PbxExtensions();

        $ext->name = $item['name'] ?? null;
        $ext->ext = $item['ext'] ?? null;
        $ext->extensionid = $index;
        $ext->email = $item['email'] ?? null;
        $ext->status = $item['status'] ?? null;
        $ext->api_id = $index;
        $ext->linenum = $item['linenum'] ?? null;
        $ext->location = $item['location'] ?? null;
        $ext->auto_provisioning = $details['options']['autoprovisiong'] ?? null;
        $ext->macaddress = $item['macaddress'] ?? null;
        $ext->protocol = $item['protocol'] ?? null;
        $ext->pin = $details['pin'] ?? null;
        $ext->ua_fullname = $item['ua_fullname'] ?? null;
        $ext->ua_id = $item['ua_id'] ?? null;
        $ext->ua_name = $item['ua_name'] ?? null;
        $ext->dhcp = $details['options']['dhcp'] ?? null;
        $ext->static_ip = $details['options']['staticip'] ?? null;
        $ext->company_id = $tenant->company_id;
        $ext->pbx_tenant_code = $tenant->tenant_id;
        $ext->pbx_server_id = $tenant->pbx_server_id;

        $ext->save();

        return $ext;

    }

    public function updateCache(int $id)
    {
        $value = Cache::get('tenant_synchronize') ?? [];
        $value = array_unique(array_merge($value, [$id]));
        Cache::put('tenant_synchronize', $value, 3600);

    }

    public function deleteDid(array $ids, PbxServerTenant $tenant)
    {
        $toDelete = PbxExtensions::query()
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->whereIn('extensionid', $ids)
            ->get();

        foreach ($toDelete as $item) {

            PbxServicesExtensions::query()
                ->where('pbx_extension_id', $item->id)
                ->delete();

            if ($item->pbxService->id ?? null) {
                $this->updateCache($item->pbxService->id);
            }

            $item->deleted_in_server = true;
            $item->deleted_at = now();
            $item->save();

        }
    }
}
