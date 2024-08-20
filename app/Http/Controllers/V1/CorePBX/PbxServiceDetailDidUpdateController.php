<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\DataObject\DidDO;
use Crater\Http\Controllers\Controller;
use Crater\Models\PbxDID;
use Crater\Models\PbxServices;
use Crater\Pbxware\PbxWareApi;
use Exception;
use Illuminate\Http\Request;

class PbxServiceDetailDidUpdateController extends Controller
{
    public function __invoke(Request $request, $pbxService, $did)
    {
        try {
            //Validar Request.
            [$pbxService, $did] = $this->check($pbxService, $did);
            $request->merge(['pbxdid_id' => $did->pbxdid_id, 'server' => $pbxService->tenant->tenantid]);
            $do = new DidDO($request->all());
            $values = $do->toApi();
            $api = new PbxWareApi($pbxService->pbxPackage->pbxServer);

            return $api->didUpdate($pbxService->tenant->tenantid, $values);

        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }
    }

    /** @throws Exception */
    private function check($pbxService, $did): array
    {

        $pbxService = PbxServices::find($pbxService);
        $did = PbxDID::find($did);

        if ($did == null or $pbxService == null) {
            throw new Exception('You need to Supply a valid id for PbxService and Did');
        }

        if (request()->type != 'Extension') {
            throw new Exception('Only Type Extension can be selected');
        }

        if ($did->pbxdid_id == null) {
            throw new Exception('The Did pbxdid_id Is required');
        }

        if (($pbxService->tenant->tenantid ?? null) == null) {
            throw new Exception('The Tenant tenantid Is required');
        }

        return [$pbxService, $did];
    }
}
