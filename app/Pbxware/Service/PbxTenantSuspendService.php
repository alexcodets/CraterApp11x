<?php

namespace Crater\Pbxware\Service;

use Crater\Models\PbxServices;
use Crater\Models\PbxTenant;
use Crater\Pbxware\PbxWareApi;
use Exception;

class PbxTenantSuspendService
{
    protected $tenant;

    protected $api;
    public const VERSION = '6.6.1.0';

    public function suspend($pbxService)
    {
        try {
            //$this->check($pbxService);
            /** @var PbxTenant $tenant */
            list($tenant, $api) = $this->check($pbxService);
            //return $tenant;

            return $tenant->suspend($api);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }

    public function unsuspend($pbxService)
    {
        try {
            //$this->check($pbxService);
            /** @var PbxTenant $tenant */
            list($tenant, $api) = $this->check($pbxService);

            return $tenant->unsuspend($api);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }

    public function isNewVersion(PbxWareApi $api): bool
    {
        $response = $api->getLicenseInfo();
        if ($response['success'] === false) {
            return 0;
        }

        if (version_compare(strtok($response['data']['Version'], ' '), self::VERSION, '>=')) {
            return true;
        }

        return false;
    }

    /**
     *
     * @param mixed $id
     * @return array
     * @throws \Exception
     */
    private function check($id): array
    {
        $pbxService = PbxServices::find($id);
        if ($pbxService == null) {
            throw new Exception('PbxService Id is not valid');
        }

        $tenant = $pbxService->tenant;

        if ($tenant == null) {
            throw new Exception('PbxService does not have a valid tenant');
        }


        $server = $pbxService->pbxPackage->pbxServer;

        if ($server == null) {
            throw new Exception('PbxService does not have a valid Server');
        }

        /** @var PbxTenant $tenant */
        /** @var \Crater\Models\PbxServers $server */

        return [$tenant, new PbxWareApi($server)];

    }
}
