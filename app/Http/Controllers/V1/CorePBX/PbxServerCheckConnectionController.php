<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServers;
use Crater\Services\Pbx\PbxCheckConnectionService;

//use Symfony\Component\HttpFoundation\Response;

class PbxServerCheckConnectionController extends Controller
{
    public function __invoke(PbxServers $pbxServer)
    {
        $service = new PbxCheckConnectionService();

        try {
            $checkServer = $service->checkServer($pbxServer);
            $checkServer['status'] = 200;

            $this->changeServerStatus($pbxServer, $checkServer);

            return response()->json($checkServer, $checkServer['status']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json($this->response(false, $th->getMessage(), [], $th->getCode()), $th->getCode());
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

    private function changeServerStatus($pbxServer, $apicheck)
    {

        if($apicheck['success'] == true) {
            $new_Status = $pbxServer->status = 'A';
            $pbxServer->save();
        }

        if($apicheck['success'] == false) {
            $new_Status = $pbxServer->status = 'I';
            $pbxServer->save();
        }

        return $new_Status;
    }
}
