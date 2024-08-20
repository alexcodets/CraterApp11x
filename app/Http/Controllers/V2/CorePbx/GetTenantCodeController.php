<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxServers;

class GetTenantCodeController extends Controller
{
    public function __invoke(PbxServers $pbxServer)
    {
        $api = new \Crater\Pbxware\PbxWareApi($pbxServer);

        try {
            $response = $api->tenantList();
            $data = collect($response['data']);

            if ($response['success'] === false) {
                return response()->json($response);
            }

            $response['message'] = 'Tenant code list.';

            $response['data'] = $data->map(function (array $item, int $key) {
                return [
                    'tenant_code' => $item['tenantcode'],
                    'id' => $key,
                    'name' => $item['name']
                ];
            })->toArray();

            return response()->json($response);
        } catch (\Throwable $th) {
            \Log::debug($th);

            return response()->json([]);
        }
    }
}
