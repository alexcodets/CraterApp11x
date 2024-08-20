<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxCdrTenant;
use Illuminate\Http\Response;

class PbxCommandController extends Controller
{
    public function tenantImportCdr($tenant): array
    {
        $pbxCdrTenant = PbxCdrTenant::find($tenant);
        if (is_null($pbxCdrTenant)) {
            return $this->response(false, 'Tenant Not found', [], Response::HTTP_NOT_FOUND);
        }

        if ($pbxCdrTenant->job_active_at) {
            return $this->response(false, 'Tenant already have active Jobs', [], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        //TODO: segun sea necesario, la idea es capturar todos los posibles valores, armar el array, limpiar el array de nulls y luego enviarlo.
        $parameters = [
            '--days' => request('days', 90),
            '--tenantid' => $tenant,
        ];

        \Artisan::call('pbx:TenantImportCDRs', $parameters);

        return $this->response(true, 'The jobs where created');

    }

    private function response(bool $success, string $message, array $data = [], int $code = Response::HTTP_OK): array
    {
        return [
            'success' => $success,
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ];
    }
}
