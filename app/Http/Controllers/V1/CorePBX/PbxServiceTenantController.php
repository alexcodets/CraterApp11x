<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Pbxware\Service\PbxTenantSuspendService;
use Illuminate\Http\Request;

class PbxServiceTenantController extends Controller
{
    protected $tenant;

    protected $api;

    public function suspend($pbxService, PbxTenantSuspendService $pbxTenantSuspendService)
    {
        try {
            return $pbxTenantSuspendService->suspend($pbxService);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }

    public function unsuspend(Request $request, $pbxService, PbxTenantSuspendService $pbxTenantSuspendService)
    {
        try {
            return $pbxTenantSuspendService->unsuspend($pbxService);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }
}
