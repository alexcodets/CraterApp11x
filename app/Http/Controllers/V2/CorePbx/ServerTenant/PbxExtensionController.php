<?php

namespace Crater\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Actions\ExtensionUpdate;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ExtensionUpdateRequest;
use Crater\Http\Requests\GenericIndexRequest;
use Crater\Http\Resources\PbxExtensionResource;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServerTenant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PbxExtensionController extends Controller
{
    use AuthorizesRequests;

    public function index(GenericIndexRequest $request, PbxServerTenant $tenant)
    {
        $this->authorize('view', $tenant);
        $fields = ['id', 'name', 'extensionid', 'status', 'created_at', 'email', 'ext'];
        $select = ['pbx_extensions.id', 'name', 'extensionid', 'status', 'created_at', 'email', 'ext', 'ua_id',
                   'protocol', 'pin', 'macaddress', 'auto_provisioning', 'location', 'dhcp', 'static_ip', 'time_zone'];
        $sort = $request->getSort($fields);

        return PbxExtensionResource::collection(PbxExtensions::query()
            ->where('pbx_tenant_code', $tenant->tenant_id)
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->select($select)
            ->withCount('pbxService')
            ->with('pbxSingleService:pbx_services.id,status,pbx_services_number,pbx_tenant_id,customer_id')
            ->orderBy($sort['sort'], $sort['order'])
            ->paginate($request->input('limit', 15)));

    }

    public function show(PbxServerTenant $tenant, PbxExtensions $extension)
    {
        $this->authorize('viewExtension', [$tenant, $extension]);
        $extension->loadCount('pbxService');

        return new PbxExtensionResource($extension);

    }

    public function update(ExtensionUpdateRequest $request, PbxServerTenant $tenant, PbxExtensions $extension, ExtensionUpdate $action)
    {
        $this->authorize('viewExtension', [$tenant, $extension]);

        try {

            return response()->json($action->handle($tenant->pbxTenant, $extension, $request));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'code' => $th->getCode()], 500);
        }

    }
}
