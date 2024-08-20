<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServers;
use Crater\Models\PbxTenant;
use Crater\Pbxware\PbxWareApi;
use Exception;
use Log;

class PbxTenantAppsController extends Controller
{
    public function index($pbxTenant)
    {
        try {
            $this->check($pbxTenant);
            /** @var PbxWareApi $api */
            list($tenant, $api) = $this->check($pbxTenant);

            return $api->getApps($tenant->code);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    /**
     *
     * @param mixed $id
     * @return array
     * @throws \Exception
     */
    private function check($id): array
    {
        $tenant = PbxTenant::find($id);

        if ($tenant == null) {
            throw new Exception('PbxService does not have a valid tenant');
        }

        if ($tenant->code == null) {
            throw new Exception('PbxService does not have a valid tenant code');
        }
        //Log::debug( $tenant);
        //Log::debug( $tenant->pbx_server_id);

        $server = PbxServers::where("id", $tenant->pbx_server_id)->first();

        //Log::debug( $server);

        if ($server == null) {
            throw new Exception('PbxService does not have a valid Server');
        }

        /** @var \Crater\Models\PbxTenant $tenant */
        /** @var PbxWareApi $api */

        return [$tenant, new PbxWareApi($server)];

    }
}
