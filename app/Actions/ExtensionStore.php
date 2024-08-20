<?php

namespace Crater\Actions;

use Crater\DataObject\ExtensionStoreData;
use Crater\Models\PbxExtensions;
use Crater\Pbxware\PbxWareApi;

class ExtensionStore
{
    public function handle(ExtensionStoreData $data): array
    {
        try {
            $api = new PbxWareApi($data->pbxServer);
            $ua = $api->getUserAgentDevicesEnable($data->pbxTenant->tenantid);
            if (! $ua['success']) {
                $ua['message'] = 'Error from api: '.$ua['message'];

                return $ua;
            }

            $ua = $ua['data'][$data->ua_id] ?? null;
            if (is_null($ua)) {
                return [
                    'success' => false,
                    'message' => 'Could not find ua with id '.$data->ua_id,
                ];
            }
            $data->ua_fullName = $ua['fullname'];
            $data->ua_name = $ua['name'];

            $api = new PbxWareApi($data->pbxServer);
            $response = $api->extensionStore($data->pbxTenant->tenantid, $data->dataToApi());

            if (! $response['success']) {
                $response['message'] = 'Error from api: '.$response['message'];

                return $response;
            }

            $data->extensionId = $response['data']['id'];
            $data->extensionExt = $response['data']['ext'];

            $response['data']['extension'] = PbxExtensions::create($data->dataToModel());
        } catch (\Throwable $th) {
            \Log::error($th->getTraceAsString());

            return ['success' => false, 'message' => $th->getMessage()];

        }

        return $response;

    }
}
