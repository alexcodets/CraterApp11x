<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Http\Requests\PbxServerTenantRequest;
use Crater\Http\Requests\PbxServerTenantShowRequest;
use Crater\Http\Resources\PbxServerTenantResource;
use Crater\Models\PbxServerTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Spatie\QueryBuilder\QueryBuilder;

class PbxServerTenantController
{
    use AuthorizesRequests;

    public function index(PbxServerTenantShowRequest $request)
    {
        //$this->authorize('viewAny', PbxServerTenant::class);
        $fields = ['id', 'created_at', 'name', 'tenant_code', 'tenant_id', 'status', 'created_at'];
        //, 'server_name', 'in_use'

        $data = QueryBuilder::for(PbxServerTenant::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($request->name, function (Builder $query) use ($request) {
                return $query->where('name', 'like', '%'.$request->name.'%');
            })->when($request->code, function (Builder $query) use ($request) {
                return $query->where('tenant_code', 'like', '%'.$request->code.'%');
            })->when($request->server, function (Builder $query) use ($request) {
                return $query->whereHas('pbxServer', function (Builder $query) use ($request) {
                    $query->where('server_label', 'like', '%'.$request->server_name.'%');
                });
            })->with([
                'pbxServer:id,server_label,status',
            ]))
            ->defaultSort('-created_at')
            ->allowedSorts($fields)
            ->paginate(request()->input('limit', 15));

        return PbxServerTenantResource::collection($data);
    }

    public function store(PbxServerTenantRequest $request)
    {

        //$this->authorize('create', PbxServerTenant::class);

        return new PbxServerTenantResource(PbxServerTenant::create($request->validated()));
    }

    public function show(PbxServerTenant $pbxServerTenant)
    {
        $this->authorize('view', $pbxServerTenant);

        return new PbxServerTenantResource($pbxServerTenant);
    }

    public function update(PbxServerTenantRequest $request, PbxServerTenant $pbxServerTenant)
    {
        //$this->authorize('update', $pbxServerTenant);

        $pbxServerTenant->update($request->validated());

        return new PbxServerTenantResource($pbxServerTenant);
    }

    public function destroy(PbxServerTenant $pbxServerTenant)
    {
        //$this->authorize('delete', $pbxServerTenant);

        $pbxServerTenant->delete();

        return response()->json();
    }
}
