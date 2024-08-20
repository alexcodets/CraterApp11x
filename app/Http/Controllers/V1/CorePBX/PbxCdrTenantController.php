<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Http\Resources\PbxCdrTenantCollection;
use Crater\Models\PbxCdrTenant;
use Illuminate\Http\Request;

class PbxCdrTenantController extends Controller
{
    public function index()
    {
        return new PbxCdrTenantCollection(PbxCdrTenant::paginate(request('limit', 10)));
    }

    public function update($id, Request $request)
    {
        throw new \Exception('Method not implemented');
    }
}
