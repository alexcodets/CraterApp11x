<?php

//use Illuminate\Foundation\Testing\RefreshDatabase;

//uses(RefreshDatabase::class);

use Crater\Models\PbxPackages;
use Crater\Models\PbxServices;
use Crater\Models\PbxTenant;

test('Retun info if there is no valid service', function () {
    $this->Artisan('pbx:importCDRs')
        ->expectsOutput(trans('pbxImportCdrs.errors.service.empty'));
});

test('Retun Error if the input service is no a valid pbx service', function () {
    $this->Artisan('pbx:importCDRs --service=3')
        ->expectsOutput(trans('pbxImportCdrs.errors.service.not_found'));
});

test('Retun Error if the input service is no active', function () {
    $package = PbxPackages::factory()->has(PbxServices::factory())->create([
        'rate_per_minutes' => null,
        'minutes_increments' => 0,
        'status' => 'I',
        'call_ratings' => 0,
    ]);

    $this->Artisan('pbx:importCDRs --service=1')
        ->expectsOutput(trans('pbxImportCdrs.errors.service.call_rating'))
        ->expectsOutput(trans('pbxImportCdrs.errors.service.status'))
        ->expectsOutput(trans('pbxImportCdrs.errors.service.rate_per_minutes'))
        ->expectsOutput(trans('pbxImportCdrs.errors.service.minutes_increments'));

    PbxPackages::first()->update(['status' => 'A']);

    $this->Artisan('pbx:importCDRs --service=1')
        ->doesntExpectOutput(trans('pbxImportCdrs.errors.service.status'));

    PbxPackages::first()->update(['minutes_increments' => 1]);

    $this->Artisan('pbx:importCDRs --service=1')
        ->doesntExpectOutput(trans('pbxImportCdrs.errors.service.minutes_increments'));

    PbxPackages::first()->update(['call_ratings' => 1]);

    $this->Artisan('pbx:importCDRs --service=1')
        ->doesntExpectOutput(trans('pbxImportCdrs.errors.service.call_rating'));

});

test('Return Error if the inputs have wrong format', function () {
    $this->Artisan('pbx:importCDRs --service=asd --days=ari --hours=gato --minutes==gosai')
        ->expectsOutput(trans('pbxImportCdrs.errors.service_int'))
        ->expectsOutput(trans('pbxImportCdrs.errors.days_int'))
        ->expectsOutput(trans('pbxImportCdrs.errors.hours_int'))
        ->expectsOutput(trans('pbxImportCdrs.errors.minutes_int'));
});

test('It work', function () {
    Artisan::call('db:seed', ['--class' => 'PbxPackageServerSeeder', '--force' => true]);

    PbxServices::factory()->create([
        'pbx_package_id' => PbxPackages::first()->id,
        'pbx_tenant_id' => PbxTenant::first()->id,
        'status' => 'A',
        'term' => 'Months',
        'date_begin' => now(),
        'allow_discount' => 0,
        'allow_discount_value' => 1,
        'date_from' => now(),
        'date_to' => null,
        'time_period' => 1,
        'time_period_value' => 'Months',
    ]);
    $this->Artisan('pbx:importCDRs')
        ->expectsOutput(trans('pbxImportCdrs.calculate.totals', ['total' => 1]));
});
