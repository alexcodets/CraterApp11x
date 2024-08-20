<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Models\PbxServers;
use Illuminate\Http\JsonResponse;

class PbxDidGroupsController
{
    public function index(PbxServers $pbxServer): JsonResponse
    {
        $api = new \Crater\Pbxware\PbxWareApi($pbxServer);

        try {
            return response()->json($api->getDidGroups());
        } catch (\Throwable $th) {
            \Log::debug($th);

            return response()->json([], 500);
            //throw $th;
        }
    }
}
