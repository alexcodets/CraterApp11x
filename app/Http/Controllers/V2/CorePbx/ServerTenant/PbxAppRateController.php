<?php

namespace Crater\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GenericIndexRequest;
use Crater\Http\Resources\PbxAppRateResource;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesAppRate;
use Illuminate\Http\Request;

class PbxAppRateController extends Controller
{
    public function index(GenericIndexRequest $request, PbxServerTenant $tenant)
    {
        //return PbxServicesAppRateResource::collection(PbxServicesAppRate::all());
        $fields = ['id', 'app_name', 'quantity', 'cost', 'created_at', 'pbx_service_id'];
        $sort = $request->getSort($fields);
        $services = PbxServices::whereHas('tenant', function ($query) use ($tenant) {
            return $query->where('pbx_server_id', $tenant->pbx_server_id)
                ->where('tenantid', $tenant->tenant_id)
                ->where('code', $tenant->tenant_code);
        })->get('id');

        return PbxAppRateResource::collection(
            PbxServicesAppRate::whereIn('pbx_service_id', $services->pluck('id'))
                ->where('quantity', '>', 0)
                ->orderBy($sort['sort'], $sort['order'])
                ->with('pbxService:id,status,pbx_services_number,pbx_tenant_id,customer_id')
                ->paginate($request->input('limit', 15))
        );

    }

    public function store(Request $request)
    {
    }

    public function show(PbxServicesAppRate $appRate)
    {
    }

    public function update(Request $request, PbxServicesAppRate $pbxServicesAppRate)
    {
    }

    public function destroy(PbxServicesAppRate $pbxServicesAppRate)
    {
    }
}
