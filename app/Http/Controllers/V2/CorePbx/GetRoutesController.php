<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServers;

class GetRoutesController extends Controller
{
    public function __invoke(PbxServers $pbxServer)
    {
        $api = new \Crater\Pbxware\PbxWareApi($pbxServer);

        try {
            return response()->json($api->getRoutes());
        } catch (\Throwable $th) {
            \Log::debug($th);

            return response()->json([]);
        }
    }
}
