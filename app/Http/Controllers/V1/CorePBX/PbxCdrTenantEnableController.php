<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxCdrTenant;
use Symfony\Component\HttpFoundation\Response;

class PbxCdrTenantEnableController extends Controller
{
    public function enable($id)
    {
        $pbxCdrTenant = PbxCdrTenant::find($id);

        if (is_null($pbxCdrTenant)) {
            return $this->errorResponse('Invalid ID', Response::HTTP_NOT_FOUND);
        }

        if ($pbxCdrTenant->status === 1) {
            return $this->errorResponse('The tenant is already enabled', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        PbxCdrTenant::where('id', $id)->update([
            'status' => 1,
        ]);

        return $this->successResponse('The PbxCdrTenant was enabled', Response::HTTP_OK);

    }

    public function disable($id)
    {
        $pbxCdrTenant = PbxCdrTenant::find($id);

        if (is_null($pbxCdrTenant)) {
            return $this->errorResponse('Invalid ID', Response::HTTP_NOT_FOUND);
        }

        if ($pbxCdrTenant->status === 0) {
            return $this->errorResponse('The tenant is already disable', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        PbxCdrTenant::where('id', $id)->update([
            'status' => 0,
        ]);

        return $this->successResponse('The PbxCdrTenant was disabled', Response::HTTP_OK);

    }

    private function errorResponse(string $message, $code, $data = ''): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ];
    }

    private function successResponse(string $message, $code = Response::HTTP_OK, array $data = []): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
            'code' => $code,
        ];
    }
}
