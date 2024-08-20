<?php

namespace Crater\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Http\Controllers\Controller;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServerTenant;
use Illuminate\Http\JsonResponse;

class PbxDidDestinationTypesController extends Controller
{
    public function index(PbxServerTenant $tenant, string $type)
    {
        $api = new \Crater\Pbxware\PbxWareApi($tenant->pbxServer);

        switch ($type) {
            case 'extension':
                $data = PbxExtensions::query()
                    ->where('pbx_server_id', $tenant->pbx_server_id)
                    ->where('pbx_tenant_code', $tenant->tenant_code)
                    ->selectRaw('ext as value, CONCAT(ext, " - ", name) as label')
                    ->get();

                return response()->json([
                    'success' => true,
                    'type' => 'Extension List',
                    'data' => $data,
                ]);

                break;
                //(multi user)
            case 'forward-did':
            case 'ring-groups':
                try {
                    $data = $api->getRingGroups($tenant->tenant_id);

                    return $this->getResponse($data, 'Ring group');

                    //ext - name
                } catch (\Throwable $th) {
                    \Log::debug($th);

                    return response()->json(['message' => $th->getMessage()], 500);
                }
            case 'ivr':
                try {
                    $data = $api->getIvr($tenant->tenant_id);

                    return $this->getResponse($data, 'Ivr group');
                } catch (\Throwable $th) {
                    \Log::debug($th);

                    return response()->json(['message' => $th->getMessage()], 500);
                }
            case 'queues':
                //code
                return response()->json([
                    'data' => [
                    ],
                    'message' => 'This feature is deprecated. Please use Enhanced Ring Groups! ',
                ]);

                break;
            case 'external-number':
                return response()->json([
                    'data' => [
                    ],
                    'message' => 'Just write the number',
                ]);

                break;
            case 'ivr-tree':
                //code
                break;

                //code
                break;
            default:
                return response()->json([], 404);
        }

        return response()->json([], 404);
    }

    private function getResponse(array $data, string $type): JsonResponse
    {
        if (! $data['success']) {
            return response()->json(['success' => false, 'message' => $data['message']], 500);
        }
        $response = [
            'success' => true,
            'type' => $type.' List',
            'data' => []
        ];
        foreach ($data['data'] as $key => $value) {
            $response['data'][] = [
                'value' => $value['ext'],
                'label' => $value['ext'].' - '.$value['name'],
            ];
        }

        return response()->json($response);
    }
}
