<?php

namespace Crater\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Actions\DidUpdate;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GenericIndexRequest;
use Crater\Http\Requests\PbxDidUpdateRequest;
use Crater\Http\Resources\PbxDidResource;
use Crater\Models\PbxDID;
use Crater\Models\PbxServerTenant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class PbxDidController extends Controller
{
    use AuthorizesRequests;

    public function index(GenericIndexRequest $request, PbxServerTenant $tenant)
    {
        $this->authorize('view', $tenant);
        $fields = ['id', 'server', 'ext', 'number', 'api_id', 'created_at', 'status', 'trunk', 'type'];
        $sort = $request->getSort($fields);
        $select = ['id', 'server', 'ext', 'number', 'number2', 'api_id', 'created_at', 'status', 'trunk', 'type', 'e164', 'e164_2', 'disabled', 'callerid'];

        return PbxDidResource::collection(PbxDID::query()
            ->where('pbx_server_id', $tenant->pbx_server_id)
            ->where('pbx_tenant_code', $tenant->tenant_id)
              ->select($fields)
            ->with(['pbxServiceDid:id,pbx_did_id,pbx_service_id', 'pbxServiceDid.service:id,status,pbx_services_number,pbx_tenant_id,customer_id'])
            ->orderBy($sort['sort'], $sort['order'])
            ->paginate($request->input('limit', 15)));
    }

    public function store(Request $request)
    {
    }

    public function show(PbxServerTenant $tenant, PbxDID $did)
    {
        $this->authorize('viewDid', [$tenant, $did]);

        return new PbxDidResource($did);
    }

    public function update(PbxDidUpdateRequest $request, PbxServerTenant $tenant, PbxDID $did, DidUpdate $action)
    {
        $this->authorize('viewDid', [$tenant, $did]);

        try {
            return response()->json($action->handle($tenant->pbxTenant, $did, $request));
        } catch (\Throwable $th) {
            return response()->json(['error' => $th->getMessage(), 'code' => $th->getCode()], 500);
        }
    }

    public function destroy(PbxDID $pbxDID)
    {
    }
}
