<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServices;
use Crater\Services\Pbx\PbxCheckConnectionService;
use Symfony\Component\HttpFoundation\Response;

class PbxServiceCheckConnectionController extends Controller
{
    public function __invoke(PbxServices $pbxService): \Illuminate\Http\JsonResponse
    {

        if ($pbxService->pbxPackage == null) {
            $response = $this->response(false, 'PbxPackage for Service not found', [], Response::HTTP_NOT_FOUND);

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        $server = $pbxService->pbxPackage->pbxServer;

        if ($server == null) {
            $response = $this->response(false, 'PbxServer for PbxPackage from the PbxService not found', [], Response::HTTP_NOT_FOUND);

            return response()->json($response, Response::HTTP_NOT_FOUND);
        }

        $service = new PbxCheckConnectionService();

        try {
            $response = $service->checkServer($server);

            return response()->json($response, $response['status']);

        } catch (\Throwable $th) {
            //throw $th;
            $response = $this->response(false, $th->getMessage(), [], Response::HTTP_UNPROCESSABLE_ENTITY);

            return response()->json($response, Response::HTTP_UNPROCESSABLE_ENTITY);

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
