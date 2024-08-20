<?php

namespace Crater\Services\Pbx;

use Crater\DataObject\ExtensionDO;
use Crater\Http\Requests\ExtensionUpdateRequest;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxTenant;
use Crater\Pbxware\PbxWareApi;

class UpdateExtensionService
{
    public function handle(PbxTenant $tenant, PbxExtensions $ext, ExtensionUpdateRequest $request): array
    {
        try {
            $api = new PbxWareApi($tenant->pbxServer);
            $details = $api->extensionConfiguration($tenant->tenantid, $ext->pbxext_id);

            if (! $details['success']) {
                $details['message'] = 'Error from api: '.$details['message'];

                return $details;
            }

            $request->merge(['pbxext_id' => $ext->pbxext_id, 'server' => $tenant->tenantid, 'codecs' => $details['data'][$ext->pbxext_id]['options']['allow']]);

            $values = ExtensionDO::fromRequestToApi($request);
            \Log::debug('Before api');
            $response = $api->extensionUpdate($tenant->tenantid, $values);
            //$details['data'][3]['options']['allow']
            if ($response['success']) {
                $ext->update(array_filter([
                    'name' => $request->name,
                    'email' => $request->email,
                    'ext' => $request->ext,
                    'location' => $request->location,
                    'ua_id' => $request->ua_id,
                    'status' => $request->status,
                    'protocol' => $request->protocol,
                    'macaddress' => $request->mac_address,
                    'pin' => $request->pin,
                ]));
            }

            return $response;
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }
}
