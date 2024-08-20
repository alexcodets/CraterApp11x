<?php

namespace Crater\Http\Controllers\V2\CorePbx;

use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;

class PbxServerTenantIncompleteController
{
    public function index(PbxServers $pbxServer)
    {
        return PbxServerTenant::where('pbx_server_id', $pbxServer->id)
            ->where('status', PbxServerTenant::STATUS_INCOMPLETE)
            ->get();
    }

    public function destroy(PbxServerTenant $tenant)
    {
        $tenant->status = PbxServerTenant::STATUS_ACTIVE;
        $tenant->completed_by = auth()->id();
        $tenant->completed_at = now();
        $tenant->save();

        return response()->json(['success' => true]);
    }
}
