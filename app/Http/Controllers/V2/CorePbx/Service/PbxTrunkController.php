<?php

namespace Crater\Http\Controllers\V2\CorePbx\Service;

use Crater\Models\PbxServices;
use Crater\Pbxware\PbxWareApi;

class PbxTrunkController
{
    public function index(PbxServices $pbxService)
    {
        $api = new PbxWareApi($pbxService->tenant->pbxServer);

        return response()->json($api->getTrunks());
    }
}
