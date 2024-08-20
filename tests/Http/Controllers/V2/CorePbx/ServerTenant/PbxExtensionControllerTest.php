<?php

namespace Tests\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Models\Company;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxTenant;
use Crater\Models\User;

test('index', function () {
    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
    ]);
    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);
    $tenant = PbxTenant::factory()->create([
        'creator_id' => $user->id,
        'company_id' => $user->company_id,
        'pbx_server_id' => $server->id,
    ]);
    $serverTenant = PbxServerTenant::factory()->create([
        'tenant_id' => $tenant->tenantid,
        'tenant_code' => $tenant->code,
        'status' => 2,
        'pbx_server_id' => $server->id,
    ]);

    PbxExtensions::factory()->create([
        'pbx_tenant_id' => $tenant->id,
        'pbx_server_id' => $server->id,
        'company_id' => $user->company_id,
        'creator_id' => $user->id,
    ]);
});
