<?php

namespace Crater\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Models\PbxServerTenant;

class GetUserAgentDeviceController
{
    public function __invoke(PbxServerTenant $tenant)
    {
        $api = new \Crater\Pbxware\PbxWareApi($tenant->pbxServer);

        try {
            $response = $api->getUserAgentDevices($tenant->tenant_id);
            if (! $response['success']) {
                $response['message'] = 'Error from api: '.$response['message'];

                return $response;
            }
            $response['data'] = array_filter($response['data'], fn ($item) => $item['enabled'] !== false);

            return response()->json($response);
        } catch (\Throwable $th) {
            \Log::error($th);

            return response()->json([], 500);
        }
    }
}
