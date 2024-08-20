<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServers;
use Illuminate\Http\JsonResponse;

class GetTenantPackageController extends Controller
{
    public function __invoke(PbxServers $pbxServer): JsonResponse
    {
        $api = new \Crater\Pbxware\PbxWareApi($pbxServer);

        try {
            return response()->json($api->getTenantPackage());
        } catch (\Throwable $th) {
            \Log::debug($th);

            return response()->json([], 500);
            //throw $th;
        }
    }
}
