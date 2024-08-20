<?php

use Crater\Jobs\PbxGenerateAvalaraTaxesJob;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\PbxTenant;
use Crater\Models\User;

beforeEach(function () {
    Mail::fake();
    //Event::fake();
    Queue::fake();
});

it('works', function () {


    $user = User::factory()->forCompany()->create();
    /* @var User $user */

    CustomerConfig::factory()->create([
        'customer_id' => $user->id,
        'creator_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'allow_renewal_date_job_pbx',
        'value' => 1,
        'company_id' => $user->company_id
    ]);

    CompanySetting::create([
        'option' => 'time_run_renewal_date_job_pbx',
        'value' => now()->format('H:i'),
        'company_id' => $user->company_id
    ]);

    $package = PbxPackages::factory()->create([
        'creator_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    $tenant = PbxTenant::factory()->create([
        'creator_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    PbxServices::factory()->create([
        'status' => 'A',
        'company_id' => $user->company_id,
        'customer_id' => $user->id,
        'creator_id' => $user->id,
        'pbx_package_id' => $package->id,
        'renewal_date' => now()->subDay(),
        'allow_pbx_packages_update' => true,
    ]);

    \Crater\Models\InvoiceTemplate::factory()->create();


    //new invoice
    //PbxGenerateAvalaraTaxesJob dispatched

    $this->artisan('check:pbx-service:renewal_date');

    Queue::assertPushed(PbxGenerateAvalaraTaxesJob::class);

    expect(Invoice::count())->toBe(1)
        ->and(Invoice::first())->due_date->toBe(now()->addDays(7)->format('Y-m-d'));

});
