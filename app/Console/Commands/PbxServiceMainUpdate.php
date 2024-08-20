<?php

namespace Crater\Console\Commands;

use Crater\Mail\ServiceMainUpdateMail;
use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxTenant;
use Crater\Models\ScheduleLog;
use Crater\Pbxware\PbxWareApi;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;
use Log;
use Throwable;

class PbxServiceMainUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pbx:serviceMainUpdate';
    //Syn/update

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and compare DID and Extension for each system, if there are new Add them, if any was deleted them alter the value in a FIELD.';

    public PbxServices $service;

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
        $pbxServices = PbxServices::where('main_update', true)->get();

        if ($pbxServices->isEmpty()) {
            //Log::debug('Not result');
            return self::SUCCESS;
        }

        foreach ($pbxServices as $service) {
            /* @var PbxServices $service */
            $this->service = $service;
            //$server = $service->pbxPackage->pbxServer;
            $api = new PbxWareApi($service->pbxPackage->pbxServer);
            $response = $api->checkConnection();
            if (! $response['success']) {
                //Log the error.
                $this->service->scheduleLogs()->create([
                    'module_name' => 'pbx:serviceMainUpdate',
                    'lvl' => ScheduleLog::LVL_ERROR,
                    'message' => __('comandos.PbxServiceMainUpdate.api.connection', ['serviceId' => $service->id, 'message' => $response['message']])
                ]);
                //Log::debug("Command:pbx:serviceMainUpdate with PbxServices id: {$service->id} have a connection problem with the error: {$response['message']}");
            }

            //Log::debug('It work i guess');

            try {
                $did = $this->processDid($api, $this->service->tenant);
                $ext = $this->processExtensions($api, $this->service->tenant);
                $this->sendNotification($did, $ext, $service);

            } catch (Throwable $th) {
                //Log::debug($th->getMessage());
                $this->service->scheduleLogs()->create([
                    'module_name' => 'pbx:serviceMainUpdate',
                    'lvl' => ScheduleLog::LVL_ERROR,
                    'message' => $th->getMessage()
                ]);
                //throw $th;
            }
            /* @var PbxServices $service */

        }

        return self::SUCCESS;
    }

    /**
     * @throws Exception
     */
    public function processDid(PbxWareApi $api, PbxTenant $tenant): array
    {
        //return $api->getDids($package->PbxService->tenant->code);
        $response = $api->didList($tenant->code);
        Log::debug('Inside Did');
        if (! $response['success']) {
            throw new Exception(__('comandos.PbxServiceMainUpdate.api.connection', ['serviceId' => $this->service->id, 'message' => $response['message']]));
        }
        $serverDids = $response['data'];

        /* @var $dids Collection */
        $dids = $tenant->pbxDids;
        $idsToDelete = [];
        $didToDelete = [];
        $newDid = [];

        //
        $dids->each(function ($did) use (&$serverDids, &$idsToDelete, &$didToDelete) {
            //$serverDid = $serverDids[$did->pbxdid_id];
            if (is_null($serverDids[$did->pbxdid_id] ?? null)) {
                $idsToDelete[] = $did->id;
                $didToDelete[] = $did;
            } else {
                //Unset all the did that are in the Core system.
                unset($serverDids[$did->pbxdid_id]);
            }
        });

        //Update to set as deleted.
        if (! is_null($idsToDelete)) {
            PbxDID::whereIn('id', $idsToDelete)->update(['deleted_in_server' => true, 'deleted_at' => now()]);
        }
        if (! is_null($idsToDelete)) {
            PbxServicesDID::whereIn('pbx_did_id', $idsToDelete)->update(['deleted_at' => now()]);
        }

        foreach ($serverDids as $key => $serverDid) {

            $newDid[] = $tenant->pbxDids()->create([
                'company_id' => $this->service->company_id,
                'pbx_server_id' => $tenant->pbx_server_id,
                'pbx_tenant_id' => $tenant->id,
                'pbx_tenant_code' => $tenant->tenantid,
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

            //Log::debug('DID Store: ', $serverDid);
        }

        return ['new' => $newDid, 'deleted' => $didToDelete];
    }

    /**
     * @throws Exception
     */
    public function processExtensions(PbxWareApi $api, PbxTenant $tenant): array
    {
        $response = $api->extensionsList($tenant->code);

        if (! $response['success']) {
            throw new Exception(__('comandos.PbxServiceMainUpdate.api.ext', ['serviceId' => $this->service->id, 'message' => $response['message']]));
        }
        /* @var $serverExtensions array */

        $serverExtensions = $response['data'];

        /* @var $dids Collection */
        $extensions = $tenant->pbxExtensions;
        $idsToDelete = [];
        $extToDelete = [];
        $extToAdd = [];

        //
        $extensions->each(function ($ext) use (&$serverExtensions, &$idsToDelete, &$extToDelete) {
            if (is_null($serverExtensions[$ext->pbxext_id] ?? null)) {
                $idsToDelete[] = $ext->id;
                $extToDelete[] = $ext;
            } else {
                //Unset all the did that are in the Core system.
                unset($serverExtensions[$ext->pbxext_id]);
            }
        });

        if (! is_null($idsToDelete)) {
            PbxExtensions::whereIn('id', $idsToDelete)->update(['deleted_in_server' => true, 'deleted_at' => now()]);
        }
        if (! is_null($idsToDelete)) {
            PbxServicesExtensions::whereIn('pbx_extension_id', $idsToDelete)->update(['deleted_at' => now()]);
        }

        foreach ($serverExtensions as $key => $serverExtension) {

            $extToAdd[] = $tenant->pbxExtensions()->create([
                'company_id' => $this->service->company_id,
                'pbx_server_id' => $tenant->pbx_server_id,
                'pbx_tenant_id' => $tenant->id,
                'pbx_tenant_code' => $tenant->tenantid,
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

            //Log::debug('Extension Store: ', $serverExtension);
        }

        return ['new' => $extToAdd, 'deleted' => $extToDelete];

    }

    public function sendNotification(array $did, array $ext, PbxServices $service)
    {
        /*Log::debug('Ext');
        Log::debug($ext);
        Log::debug('Did');
        Log::debug($did);*/
        $company = $service->company;
        if ($company->is_notification_deactivated != 'NO') {
            //Log::debug('Notification Deactivate');
            return;
        }

        if (empty($did['new']) && empty($did['deleted']) && empty($ext['new']) && empty($ext['deleted'])) {
            //Log::debug('Not Changed');
            return;
        }

        if ($company->main_email) {
            Mail::to($company->main_email)->send(new ServiceMainUpdateMail($ext, $did, $company, $company->user));
        }

        $users = $company->users()->where('pbx_notification', '=', 1)->get();

        foreach ($users as $user) {
            //Log::debug('Users');
            Mail::to($user->email)->send(new ServiceMainUpdateMail($ext, $did, $company, $company->user));
        }

    }
}
