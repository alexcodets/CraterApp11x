<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServers;
use Crater\Services\Pbx\PbxCheckConnectionService;

class PbxServerCheckLicenseController extends Controller
{
    public function __invoke(PbxServers $pbxServer): \Illuminate\Http\JsonResponse
    {

        $service = new PbxCheckConnectionService();

        try {
            $response = $service->checkLicense($pbxServer);

            return response()->json($response, $response['status']);

        } catch (\Throwable $th) {
            //throw $th;
            $response = $this->response(false, $th->getMessage(), [], $th->getCode());

            return response()->json($response, $response['status']);

        }

    }

    private function response(bool $success, string $message, array $data = [], $code = 200): array
    {
        return [
            'success' => $success,
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}
