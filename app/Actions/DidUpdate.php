<?php

namespace Crater\Actions;

use Crater\DataObject\DidData;
use Crater\Models\PbxDID;
use Crater\Models\PbxTenant;
use Crater\Pbxware\PbxWareApi;
use Illuminate\Foundation\Http\FormRequest;

class DidUpdate
{
    public function handle(PbxTenant $tenant, PbxDID $did, FormRequest $request): array
    {
        try {
            $api = new PbxWareApi($tenant->pbxServer);

            $request->merge(['pbxdid_id' => $did->pbxdid_id, 'server' => $tenant->tenantid]);

            $values = DidData::fromRequestToApi($request);
            \Log::debug('Before api');
            \Log::debug('Values: ', $values);
            $response = $api->didUpdate($tenant->tenantid, $values);
            if ($response['success']) {
                $did->update(DidData::fromRequestToModel($request, $tenant->tenantid));
            }
            \Log::debug('Values: ', $values);

            return $response;
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }
}
