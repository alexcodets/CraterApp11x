<?php

namespace Crater\Actions;

use Crater\DataObject\ExtensionDO;
use Crater\Http\Requests\ExtensionUpdateRequest;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxTenant;
use Crater\Pbxware\PbxWareApi;

class ExtensionUpdate
{
    public function handle(PbxTenant $tenant, PbxExtensions $ext, ExtensionUpdateRequest $request): array
    {
        try {
            $api = new PbxWareApi($tenant->pbxServer);
            $details = $api->extensionConfiguration($tenant->tenantid, $ext->pbxext_id);
            $ua = null;

            if ($ext->ua_id != $request->ua_id) {

                // Registrar el tenantid y ua_id para depuración
                \Log::debug('Tenant ID:', ['tenantid' => $tenant->tenantid]);
                \Log::debug('UA ID:', ['ua_id' => $request->ua_id]);

                // Obtener los dispositivos del agente de usuario
                $ua = $api->getUserAgentDevices($tenant->tenantid);
                if (! $ua['success']) {
                    $ua['message'] = 'Error from api: '.$ua['message'];

                    return $ua;
                }
                $ua = $ua['data'][$request->ua_id] ?? null;
                \Log::debug('Specific User Agent Device:', ['ua' => $ua]);
                if (is_null($ua)) {
                    return [
                        'success' => false,
                        'message' => 'Could not find ua with id '.$request->ua_id,
                    ];
                }

                $request->merge(['ua_id' => $request->ua_id, 'ua_name' => $ua['name'], 'ua_fullname' => $ua['fullname']]);
            }

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
                $ext->update(ExtensionDO::FromRequestToModel($request));
            }

            return $response;
        } catch (\Throwable $th) {
            return ['success' => false, 'message' => $th->getMessage()];
        }

    }
}
