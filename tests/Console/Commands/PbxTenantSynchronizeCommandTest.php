<?php

use Crater\Models\Company;
use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;

test('No service, no job', function () {
    Bus::fake();
    Artisan::call('tenant:synchronize');
    Bus::assertBatchCount(0);

});

test('one tenant, one batch 3 job', function () {
    Bus::fake();
    $user = \Crater\Models\User::factory()
        ->for(Company::factory(), 'company')
        ->create();

    $tenant = \Crater\Models\PbxServerTenant::factory()->create([
        'pbx_server_id' => \Crater\Models\PbxServers::factory()->create(),
        'company_id' => $user->company_id
    ]);

    Log::debug($tenant);

    //$this->artisan('inspire')->assertExitCode(0);
    Artisan::call('tenant:synchronize');
    Bus::assertBatched(function (PendingBatch $batch) {
        return $batch->name == 'Tenant-Recalculate' &&
            $batch->jobs->count() === 3;
    });

    Bus::assertBatchCount(1);

});

test('3 tenants, one batch 7 job', function () {
    Bus::fake();
    $user = \Crater\Models\User::factory()
        ->for(Company::factory(), 'company')
        ->create();

    \Crater\Models\PbxServerTenant::factory()
        ->times(3)
        ->create([
        'pbx_server_id' => \Crater\Models\PbxServers::factory()->create(),
        'company_id' => $user->company_id
    ]);

    Artisan::call('tenant:synchronize');
    Bus::assertBatched(function (PendingBatch $batch) {
        return $batch->name == 'Tenant-Recalculate' &&
            $batch->jobs->count() === 7;
    });

    Bus::assertBatchCount(1);

});

test('it works', function () {
    Bus::fake();
    $user = \Crater\Models\User::factory()
        ->for(Company::factory(), 'company')
        ->create();

    \Crater\Models\PbxServerTenant::factory()
        ->create([
            'pbx_server_id' => \Crater\Models\PbxServers::factory()->create(),
            'company_id' => $user->company_id
        ]);

    Artisan::call('tenant:synchronize');
    Bus::assertBatched(function (PendingBatch $batch) {
        return $batch->name == 'Tenant-Recalculate' &&
            $batch->jobs->count() === 7;
    });

    Bus::assertBatchCount(1);

});
