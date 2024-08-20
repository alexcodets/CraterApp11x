<?php

namespace Crater\Jobs;

use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;
use Throwable;

class ServerTenantUpdateJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ?PbxServerTenant $tenant = null;

    private ?PbxWareApi $api = null;

    private int $tenant_id;

    public function __construct(int $tenant_id)
    {
        $this->tenant_id = $tenant_id;
        \Illuminate\Support\Facades\Log::withContext([
            'tenant_id' => $this->tenant_id,
            'Module' => 'PbxServerTenantDidSynchronizeJob',
        ]);
    }

    public function handle(): void
    {
        try {
            $this->tenant = PbxServerTenant::findOrFail($this->tenant_id);
        } catch (Throwable $th) {
            Log::error("Job: ServerTenantUpdateJob, the PbxServerTenant couldn't be found.");
            Log::error($th->getMessage());

            return;
        }

        try {
            $this->api = new PbxWareApi(PbxServers::findOrFail($this->tenant->pbx_server_id));
        } catch (Throwable $th) {
            Log::error("Job: ServerTenantUpdateJob, the PbxServer with id: {$this->tenant->pbx_server_id} couldn't be found.");
            Log::error($th->getMessage());

            return;
        }

        Log::withContext([
            'tenant_code' => $this->tenant->tenant_code,
            'pbx_server_id' => $this->tenant->pbx_server_id,
            'pbx_tenant_id' => $this->tenant->id,
            'name' => self::class,
        ]);

        $response = $this->api->checkConnection();
        if (! $response['success']) {
            Log::error('The connection to the api for pbxware return a error.');
            Log::error($response['message']);

            return;
        }

        try {
            $did = $this->processDid();
            $ext = $this->processExtensions();

            Log::debug('did and ext processed', [
                'did' => [
                    'new' => $did['new'], 'deleted' => $did['deleted'],
                ],
                'ext' => [
                    'new' => $ext['new'], 'deleted' => $ext['deleted'],
                ],
            ]);
            //$this->sendNotification($did, $ext);

        } catch (Throwable $th) {
            Log::error('There was a error while downloading did and extensions ');
            Log::error($response['message']);
        }

    }

    private function processDid(): array
    {
        $response = $this->api->didList($this->tenant->tenant_code);

        if (! $response['success']) {
            Log::error("The api for did for tenant {$this->tenant->tenant_code} return a error.");
            Log::error($response['message'] ?? []);

            return [];
        }

        $serverDids = $response['data'];

        /* @var $didArray Collection */
        $didArray = $this->tenant->pbxDid;
        $idsToDelete = [];
        $didToDelete = [];
        $newDid = [];

        $didArray->each(function ($did) use (&$serverDids, &$idsToDelete, &$didToDelete) {
            if (is_null($serverDids[$did->pbxdid_id] ?? null)) {
                $idsToDelete[] = $did->id;
                $didToDelete[] = $did;
            } else {
                //Unset all the did that are in the Core system.
                unset($serverDids[$did->pbxdid_id]);
            }
        });

        //Update to set as deleted.
        if (! empty($idsToDelete)) {
            PbxDID::whereIn('id', $idsToDelete)->update(['deleted_in_server' => true, 'deleted_at' => now()]);
        }

        foreach ($serverDids as $key => $serverDid) {

            $newDid[] = PbxDID::create([
                'company_id' => $this->tenant->company_id,
                'pbx_server_id' => $this->tenant->pbx_server_id,
                'pbx_tenant_code' => $this->tenant->tenant_id,
                'api_id' => $key,
                'pbxdid_id' => $key,
                'didid' => $key,
                'number' => $serverDid['number'],
                'server' => $serverDid['server'],
                'status' => $serverDid['status'],
                'trunk' => $serverDid['trunk'],
                'type' => $serverDid['type'],
                'number2' => $serverDid['number2'],
                'ext' => $serverDid['ext'],
                'e164_2' => $serverDid['e164_2'],
            ])->toArray();

        }

        return ['new' => $newDid, 'deleted' => $didToDelete];
    }

    public function processExtensions(): array
    {
        $response = $this->api->extensionsList($this->tenant->tenant_code);

        if (! $response['success']) {
            Log::error("The api for did for tenant {$this->tenant->tenant_code} return a error.");
            Log::error($response['message'] ?? []);

            return [];
        }

        /* @var $serverExtensions array */

        $serverExtensions = $response['data'];

        /* @var $dids Collection */
        $extensions = $this->tenant->pbxExtensions;
        $idsToDelete = [];
        $extToDelete = [];
        $extToAdd = [];

        $extensions->each(function ($ext) use (&$serverExtensions, &$idsToDelete, &$extToDelete) {
            if (is_null($serverExtensions[$ext->pbxext_id] ?? null)) {
                $idsToDelete[] = $ext->id;
                $extToDelete[] = $ext;
            } else {
                //Unset all the did that are in the Core system.
                unset($serverExtensions[$ext->pbxext_id]);
            }
        });

        if (! empty($idsToDelete)) {
            PbxExtensions::whereIn('id', $idsToDelete)->update(['deleted_in_server' => true, 'deleted_at' => now()]);
        }

        foreach ($serverExtensions as $key => $serverExtension) {

            $extToAdd[] = PbxExtensions::create([
                'company_id' => $this->tenant->company_id,
                'pbx_server_id' => $this->tenant->pbx_server_id,
                'pbx_tenant_code' => $this->tenant->tenant_id,
                'api_id' => $key,
                'extensionid' => $key,
                'pbxext_id' => $key,
                'name' => $serverExtension['name'],
                'ext' => $serverExtension['ext'],
                'email' => $serverExtension['email'],
                'status' => $serverExtension['status'],
                'linenum' => $serverExtension['linenum'],
                'location' => $serverExtension['location'],
                'macaddress' => $serverExtension['macaddress'],
                'protocol' => $serverExtension['protocol'],
                'ua_fullname' => $serverExtension['ua_fullname'],
                'ua_id' => $serverExtension['ua_id'],
                'ua_name' => $serverExtension['ua_name'],
            ])->toArray();

        }

        return ['new' => $extToAdd, 'deleted' => $extToDelete];

    }
}
