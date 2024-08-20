<?php

namespace Tests\Http\Controllers\V2\CorePbx\ServerTenant;

use Crater\Models\Company;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesAppRate;
use Crater\Models\PbxTenant;
use Crater\Models\User;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\getJson;

test('it works i guess', function () {
    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
    ]);
    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);
    $package = PbxPackages::factory()->create([
        'creator_id' => $user->id,
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
    $service = PbxServices::factory()->create([
        'status' => 'A',
        'company_id' => $user->company_id,
        'customer_id' => $user->id,
        'creator_id' => $user->id,
        'pbx_package_id' => $package->id,
        'renewal_date' => now()->subDay(),
        'allow_pbx_packages_update' => true,
        'pbx_tenant_id' => $tenant->id,
    ]);

    PbxServicesAppRate::factory()->times(3)->create([
        'pbx_service_id' => $service->id,
        'pbx_package_id' => $package->id,
    ]);

    Log::debug('Tenant: ', $tenant->toArray());
    Log::debug('PbxTenant: ', $serverTenant->toArray());

    Sanctum::actingAs($user);
    $response = getJson(route('pbx-server.tenant.app-rate.index', ['tenant' => $serverTenant->id]))
        ->assertStatus(200);
    Log::debug($response->json());
});
