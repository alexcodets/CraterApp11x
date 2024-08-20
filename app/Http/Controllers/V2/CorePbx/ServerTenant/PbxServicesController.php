<?php

namespace Crater\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GenericIndexRequest;
use Crater\Http\Resources\PbxServicesResource;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PbxServicesController extends Controller
{
    use AuthorizesRequests;

    public function index(GenericIndexRequest $request, PbxServerTenant $tenant)
    {
        $this->authorize('view', $tenant);
        $columns = ['id', 'pbx_tenant_id', 'status', 'pbx_services_number', 'renewal_date', 'term', 'created_at', 'date_begin', 'customer_id'];
        $sort = $request->getSort($columns);

        return PbxServicesResource::collection(PbxServices::whereIn('pbx_tenant_id', function (Builder $query) use ($tenant) {
            $query->select('id')
                ->from('pbx_tenant')
                ->where('tenantid', $tenant->tenant_id)
                ->where('pbx_server_id', $tenant->pbx_server_id);
        })->select($columns)
            ->orderBy($sort['sort'], $sort['order'])
            ->paginate($request->input('limit', 15)));

    }

    public function show(PbxServerTenant $tenant, PbxServices $service)
    {
        $this->authorize('viewService', [$tenant, $service, $service->tenant]);

        return new PbxServicesResource($service);

    }
}
