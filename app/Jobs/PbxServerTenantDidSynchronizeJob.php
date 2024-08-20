<?php

namespace Crater\Jobs;

use Crater\Models\PbxDID;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServicesDID;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PbxServerTenantDidSynchronizeJob implements ShouldQueue
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
            'Module' => 'PbxServerTenantDidSynchronizeJob',
        ]);
    }

    public function handle(): void
    {

        Log::debug('Process Start');
        if ($this->batch()->cancelled()) {
            return;
        }
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
        $did = $api->didList($tenant->tenant_id);

        if (! $did['success']) {
            Log::error('Could not get did list from pbx server');
            Log::error($did['message']);

            return;
        }

        $did = $did['data'];

        $apiDidIds = [];

        foreach ($did as $index => $item) {

            $apiDidIds[] = $index;
            $this->createOrUpdateDid($tenant, $index, $item);

        }

        $serverDidIds = PbxDID::query()
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->pluck('api_id')->toArray();

        $toDelete = array_diff($serverDidIds, $apiDidIds);

        if ($toDelete) {
            $this->deleteDid($toDelete, $tenant);
        }
        Log::debug('Process End');

    }

    public function createOrUpdateDid(PbxServerTenant $tenant, int $index, array $item): PbxDID
    {
        $did = PbxDID::query()
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('api_id', $index)
            ->first();

        $did = $did ?: new PbxDID();

        //creator_id, company_id,
        $did->api_id = $index;
        $did->didid = $index;
        $did->pbxdid_id = $index;
        $did->number = $item['number'];
        $did->number2 = $item['number2'];
        $did->server = $item['server'];
        $did->trunk = $item['trunk'];
        $did->type = $item['type'];
        $did->ext = $item['ext'];
        $did->e164 = $item['e164'];
        $did->e164_2 = $item['e164_2'];
        $did->status = $item['status'];
        $did->pbx_tenant_code = $tenant->tenant_id;
        $did->pbx_server_id = $tenant->pbxServer->id;
        $did->company_id = $tenant->company_id;

        $did->save();

        return $did;

    }

    public function updateCache(int $id)
    {
        $value = Cache::get('tenant_synchronize') ?? [];
        $value = array_unique(array_merge($value, [$id]));
        Cache::put('tenant_synchronize', $value, 3600);

    }

    public function deleteDid(array $ids, PbxServerTenant $tenant)
    {
        $toDelete = PbxDID::query()
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->whereIn('api_id', $ids)
            ->get();

        foreach ($toDelete as $item) {

            PbxServicesDID::query()
                ->where('pbx_did_id', $item->id)
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
