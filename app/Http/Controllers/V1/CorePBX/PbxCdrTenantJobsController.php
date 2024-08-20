<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class PbxCdrTenantJobsController extends Controller
{
    public function clean($id)
    {

        \Artisan::call('pbx:ClearTenantImportCDRs', [
            '--tenant' => $id,
            '--jobs' => true,
        ]);

        return $this->successResponse('All jobs for the Tenant where deleted');
    }

    public function reactive($id)
    {

        \Artisan::call('pbx:ClearTenantImportCDRs', [
            '--tenant' => $id,
        ]);

        return $this->successResponse('Jobs will be generated again for the Tenant');

    }

    public function cleanAll()
    {
        \Artisan::call('pbx:ClearTenantImportCDRs', [
            '--jobs' => true,
        ]);

        return $this->successResponse('All jobs for the Tenants where deleted');

    }

    public function reactiveAll()
    {
        \Artisan::call('pbx:ClearTenantImportCDRs');

        return $this->successResponse('Jobs will be generated again for all the Tenants');
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
