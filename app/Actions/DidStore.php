<?php

namespace Crater\Actions;

use Crater\DataObject\DidStoreData;
use Crater\Models\PbxDID;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Support\Facades\Log;

class DidStore
{
    public function handle(DidStoreData $data): array
    {
        try {
            \Log::debug($data->dataToApi());
            $api = new PbxWareApi($data->pbxServer);
            $response = $api->didStore($data->tenant->tenantid, $data->dataToApi());

            if (! $response['success']) {
                $response['message'] = 'Error from api: '.$response['message'];

                return $response;
            }

            Log::debug('Inside DidStore', $response['data']);

            $data->didId = $response['data']['id'];

            $response['data']['did'] = PbxDID::create($data->dataToModel());
        } catch (\Throwable $th) {
            \Log::error($th->getTraceAsString());

            return ['success' => false, 'message' => $th->getMessage()];
        }

        return $response;
    }
}
