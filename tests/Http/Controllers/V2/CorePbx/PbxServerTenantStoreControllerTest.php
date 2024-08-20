<?php

namespace Tests\Http\Controllers\V2\CorePbx;

use Crater\Mail\TenantPendingActivationMail;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Crater\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

test('required inputs are required', function () {
    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
    ]);

    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);

    Sanctum::actingAs($user);
    $response = postJson(route('pbx-server.tenant.store', ['pbxServer' => $server->id]), [])
        ->assertStatus(422)
        ->assertJsonFragment([
            'tenant_name' =>
                [
                    'The tenant name field is required.',
                ],
            'tenant_code' =>
                [
                    'The tenant code field is required.',
                ],
            'package' =>
                [
                    'The package field is required.',
                ],
            'ext_length' =>
                [
                    'The ext length field is required.',
                ],
            'country' =>
                [
                    'The country field is required.',
                ],
            'national' =>
                [
                    'The national field is required.',
                ],
            'international' =>
                [
                    'The international field is required.',
                ],
        ]);
    Log::debug($response->json());
});

it('fail if tenant was not found (created) in the api', function () {
    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
    ]);

    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);

    \Mail::fake();

    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::sequence()
            ->push(['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1], 200)
            ->push(['errors' => 'algo error'], 200)
            ->push([
                ['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
            ], 200)
            ->pushStatus(404),
    ]);

    expect(PbxServerTenant::count())->toBe(0);

    $data = [
        'tenant_name' => 'HalfBad',
        'tenant_code' => 333,
        'package' => 67,
        'ext_length' => 4,
        'country' => 94,
        'national' => 1,
        'international' => '011',
    ];

    Sanctum::actingAs($user);
    $response = postJson(route('pbx-server.tenant.store', ['pbxServer' => $server->id]), $data)
        ->assertStatus(400)
        ->assertJsonFragment(['message' => 'Api error']);
    Log::debug($response->json());

    expect(PbxServerTenant::count())->toBe(0);
    Mail::assertNothingSent();

});

it('fail if tenant code is already in use', function () {
    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
    ]);

    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);

    \Mail::fake();

    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::sequence()
            ->push([
                ['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
                ['name' => 'HalfBad', 'tenantcode' => '333', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
            ], 200)
            ->push(['errors' => 'algo error'], 200)
            ->push([
                ['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
                ['name' => 'HalfBad', 'tenantcode' => '333', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
            ], 200)
            ->pushStatus(404),
    ]);

    expect(PbxServerTenant::count())->toBe(0);

    $data = [
        'tenant_name' => 'HalfBad',
        'tenant_code' => 333,
        'package' => 67,
        'ext_length' => 4,
        'country' => 94,
        'national' => 1,
        'international' => '011',
    ];

    Sanctum::actingAs($user);
    $response = postJson(route('pbx-server.tenant.store', ['pbxServer' => $server->id]), $data)
    ->assertStatus(422);
    Log::debug($response->json());

    expect(PbxServerTenant::count())->toBe(0);
    Mail::assertNothingSent();

});

it('generate the server tenant', function () {
    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
    ]);

    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);

    \Mail::fake();

    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::sequence()
            ->push(['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1], 200)
            ->push(['errors' => 'algo error'], 200)
            ->push([
                ['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
                ['name' => 'HalfBad', 'tenantcode' => '333', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
            ], 200)
            ->pushStatus(404),
    ]);

    expect(PbxServerTenant::count())->toBe(0);

    $data = [
        'tenant_name' => 'HalfBad',
        'tenant_code' => 333,
        'package' => 67,
        'ext_length' => 4,
        'country' => 94,
        'national' => 1,
        'international' => '011',
    ];

    Sanctum::actingAs($user);
    $response = postJson(route('pbx-server.tenant.store', ['pbxServer' => $server->id]), $data);
    Log::debug($response->json());

    expect(PbxServerTenant::count())->toBe(1);

});

it('sends the corresponding emails to the correct users', function () {

    $user = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
        'role2' => 'client',
    ]);

    $admin = User::factory()->create([
        'company_id' => Company::factory()->create()->id,
        'role' => 'super admin',
        'role2' => null,
    ]);

    User::factory()->create([
        'company_id' => Company::factory()->create()->id,
        'role' => 'admin',
        'pbx_notification' => true,
    ]);

    CompanySetting::factory()->create([
        'company_id' => $user->company_id,
        'option' => 'server_notification',
        'value' => true,
    ]);

    CompanySetting::factory()->create([
        'company_id' => $admin->company_id,
        'option' => 'server_notification',
        'value' => true,
    ]);

    $server = PbxServers::factory()->create([
        'company_id' => $user->company_id,
    ]);

    Mail::fake();
    Http::fake([
        'pbxdev.careonecomm.com/*' => Http::sequence()
            ->push(['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1], 200)
            ->push(['errors' => 'algo error'], 200)
            ->push([
                ['name' => 'Umbrella Corporation', 'tenantcode' => '208', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
                ['name' => 'HalfBad', 'tenantcode' => '333', 'package_id' => 67, 'package' => 'PBX-DEV', 'ext_length' => 4, 'country_id' => 869, 'country_code' => 1],
            ], 200)
            ->pushStatus(404),
    ]);
    //Queue::fake();

    expect(PbxServerTenant::count())->toBe(0);

    $data = [
        'tenant_name' => 'HalfBad',
        'tenant_code' => 333,
        'package' => 67,
        'ext_length' => 4,
        'country' => 94,
        'national' => 1,
        'international' => '011',
    ];

    Sanctum::actingAs($user);
    postJson(route('pbx-server.tenant.store', ['pbxServer' => $server->id]), $data);

    //Mail::assertSent(TenantPendingActivationMail::class, 1);
    Mail::assertQueued(TenantPendingActivationMail::class, 4);
});
