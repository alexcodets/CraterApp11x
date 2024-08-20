<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\CallDetailRegister;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Crater\Pbxware\PbxWareApi;
use Crater\Pbxware\Service\PbxService;
use Crater\Pbxware\Service\PbxWareService;

class PbxTestController extends Controller
{
    public function FunctionName(Type $var = null)
    {
        # code...
    }

    public function calculateTotal($service = 1)
    {
        $service = new PbxService();
        $response = $service->calculateTotal(PbxServices::find(1));

        return 'Finalizado';
    }

    public function extensions($pbxPackageId)
    {
        $package = PbxPackages::find($pbxPackageId);
        $api = new PbxWareApi($package->pbxServer);

        return $api->extensionsList($package->PbxService->tenant->code);
    }

    public function dids($pbxPackageId)
    {
        $package = PbxPackages::find($pbxPackageId);
        $api = new PbxWareApi($package->pbxServer);

        return $api->didList($package->PbxService->tenant->code);
    }

    public function tenants($id = '')
    {
        $tenant = 3;
        $package = PbxPackages::find($id);
        $api = new PbxWareApi($package->pbxServer);
        $user = User::find(6);
        $service = new PbxWareService();

        return $api->extensionsList($tenant);
        //return $service->importCdrs($api, $user);
        /*
        $reponse = $api->getTrunks();
        if ($reponse['success'] === false) {
        return $reponse;
        }
        return $reponse['data'];
         */
        //$response = $api->getTenantsTest($package);
        //return $response;
        //return $api->checkConnection();

        //return $api->getTrunks();

        $end = now();
        $start = now();

        $start->subDays(1);
        $start->subHours(2);
        $start->subMinutes(25);

        return $service->getCdrsByType($api, 6, $start, $end);

        return $this->gettingCdr($api);

        return $this->getCdr($api);
    }

    public function gettingCdr(PbxWareApi $api)
    {
        $tenant = 3;
        //$start     = date('M-d-Y', strtotime("-1 days"));
        //$starttime = '00:00:00';
        //$end       = date('M-d-Y', strtotime("-1 days"));
        //$endtime   = '23:59:59';
        $limit = '1000';
        $status = '8';
        $page = 0;
        //$trunkdst = ['trunkdst' => $key];
        //return $api->getCDR($tenant, $start, $starttime, $end, $endtime, $limit, $status, $page);

        $end = now();
        $start = now();

        $start->subDays(1);
        $start->subHours(2);
        $start->subMinutes(25);

        $endtime = $end->format('H:i:s');
        $end = $end->format('M-d-Y');
        $starttime = $start->format('H:i:s');
        $start = $start->format('M-d-Y');

        return $api->getCDR($tenant, $start, $starttime, $end, $endtime, $limit, $status, $page);

    }

    //Refatorizar a service
    /**
     *
     * @param array $cdrs
     * @param \Crater\Models\User $user
     * @return array
     */
    public function storeCdr(array $cdrs, User $user): array
    {
        try {
            foreach ($cdrs as $cdr) {
                CallDetailRegister::firstOrCreate([
                    'unique_id' => $cdr[7],
                ], [
                    'from' => $cdr[0],
                    'to' => $cdr[1],
                    'start_date' => $cdr[2],
                    'duration' => $cdr[3],
                    'billing_duration' => $cdr[4],
                    'cost' => $cdr[4],
                    'status' => $cdr[5],
                    'unique_id' => $cdr[6],
                    'user_id' => $user->id,
                    //company_id => $user->company_id
                ]);
            }

            return ['success' => true];
        } catch (\Throwable $th) {
            //throw $th;
            return [
                'success' => false,
                'error' => $th->getMessage(),
                'code' => $th->getCode(),
            ];
        }

    }

    public function getCdr(PbxWareApi $api)
    {
        //Tenant, $starttime, $endtime, user, tenant
        $tenant = 3;
        $start = date('M-d-Y', strtotime("-3 days"));
        $starttime = '00:00:00';
        $end = date('M-d-Y', strtotime("-1 days"));
        $endtime = '03:59:59';
        $limit = '1000';
        $status = '8';

        //$trunkdst = ['trunkdst' => $key];

        $cdrs = [];
        /*
        $trunksReponse = $api->getTrunks();
        if ($trunksReponse['success'] === false) {
        return $trunksReponse;
        }

        $trunks = $trunksReponse['data'];
         */
        //foreach ($trunks as $key => $value) {
        $next_page = true;
        $result = [];
        $page = 1;
        while ($next_page) {
            $apiResponse = $api->getCDR($tenant, $start, $starttime, $end, $endtime, $limit, $status, $page);
            if ($apiResponse['success'] === false) {
                return $cdrs;
            }
            $result = $apiResponse['data'];
            $next_page = $result['next_page'] ?? false;
            $cdrs = array_merge($cdrs, $result['csv'] ?? []);
            $page++;
        }

        //}
        return ['success' => true, 'cdrs' => $cdrs];
    }

    public function StoreCdrs(array $cdrs, $user)
    {
        foreach ($cdrs as $cdr) {
            CallDetailRegister::firstOrCreate([
                'unique_id' => $cdr[7],
            ], [
                'from' => $cdr[0],
                'to' => $cdr[1],
                'start_date' => $cdr[2],
                'duration' => $cdr[3],
                'billing_duration' => $cdr[4],
                'cost' => $cdr[4],
                'status' => $cdr[5],
                'unique_id' => $cdr[6],
                'user_id' => $user->id,
                //company_id => $user->company_id
            ]);
        }
    }
}
