<?php

use Crater\Mail\PbxTenantImportCdrMail;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\PbxCdrTenant;
use Crater\Models\PbxTenant;
use Crater\Models\PbxTenantCdr;
use Crater\Models\User;
use Database\Seeders\PbxPackageServerSeeder;
use Database\Seeders\PbxServicesSeeder;
use Illuminate\Bus\PendingBatch;

it('Create the tenant, error email is send and No cdr is downloaded', function () {
    Mail::fake();
    Bus::fake();

    $user = setSettings();
    $this->seed([
        PbxPackageServerSeeder::class,
        PbxServicesSeeder::class,
        //PbxTenantCdrSeeder::class
    ]);

    $this->artisan('pbx:TenantImportCDRs');

    expect(PbxCdrTenant::count())->toBe(1)
        ->and(PbxTenantCdr::count())->toBe(0);
    Bus::assertNothingDispatched();

});

it('Validated User', function () {
    Mail::fake();
    $user = setSettings();
    $this->seed([
        PbxPackageServerSeeder::class,
        PbxServicesSeeder::class,
        //PbxTenantCdrSeeder::class
    ]);
    PbxTenant::where('id', '>', 0)
        ->update(['status' => 1, 'code' => 218, 'tenantid' => 24]);

    $this->artisan('pbx:TenantImportCDRs');

    PbxCdrTenant::where('id', '>', 0)->update(['status' => 1]);
    //Wrong code
    $this->artisan('pbx:TenantImportCDRs');

    expect(PbxCdrTenant::count())->toBe(1)
        ->and(PbxTenantCdr::count())->toBe(0);
    Mail::assertSent(PbxTenantImportCdrMail::class, 1);

});

it('Create batch with jobs when status is active', function () {
    Mail::fake();
    Bus::fake();
    $user = setSettings();
    $this->seed([
        PbxPackageServerSeeder::class,
        PbxServicesSeeder::class,
        //PbxTenantCdrSeeder::class
    ]);

    $this->artisan('pbx:TenantImportCDRs');

    PbxCdrTenant::where('id', '>', 0)->update(['status' => 1]);
    $tenant = PbxCdrTenant::first();
    $this->artisan('pbx:TenantImportCDRs');

    expect(PbxCdrTenant::count())->toBe(1)
        ->and(PbxTenantCdr::count())->toBe(0);
    Mail::AssertNothingSent();
    Bus::assertBatched(function (PendingBatch $batch) use ($tenant) {
        return $batch->name == "Import TenantCdr for Tenant: {$tenant->id}" &&
            $batch->jobs->count() > 0;
    });

});

function setSettings(): User
{
    $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);
    $userData = $clientData[0]['client'];
    unset($userData['company_id']);
    unset($userData['creator_id']);
    unset($userData['avalara_type']);
    unset($userData['currency_id']);

    //password = ThePassword
    $user = User::factory()->for(Company::factory(), 'company')->create($userData);
    /* @var User $user */

    CompanySetting::insert([
        [
            'company_id' => $user->company_id,
            'option' => 'late_fee_hour',
            'value' => '12:21'
        ],
    ]);

    return $user;
}
