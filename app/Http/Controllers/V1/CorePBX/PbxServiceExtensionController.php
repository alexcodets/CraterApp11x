<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Pbxware\Service\PbxExtensionSuspendService;

class PbxServiceExtensionController extends Controller
{
    protected $tenant;

    protected $api;

    public function suspend($pbxService, PbxExtensionSuspendService $suspendService)
    {
        try {
            return $suspendService->suspend($pbxService);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }

    public function unsuspend($pbxService, PbxExtensionSuspendService $suspendService)
    {
        try {
            return $suspendService->unsuspend($pbxService);
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }
}
