<?php

use Crater\Models\CallDetailRegister;
use Crater\Models\CustomRateGroupItems;
use Crater\Models\PbxServicePrefixRateGroup;
use Crater\Models\PbxServices;
use Crater\Models\PrefixrateGroup;
use Database\Seeders\InternationalRateSeeder;
use Database\Seeders\PbxCDRSeeder;
use Database\Seeders\PbxDidAndExtSeeder;
use Database\Seeders\PbxPackageServerSeeder;
use Database\Seeders\PbxServicesSeeder;
use Database\Seeders\PbxUserSeeder;
use Database\Seeders\UsersTableSeeder;

it('works')->skip();

beforeEach(function () {
    Mail::fake();
    $this->seed([
        UsersTableSeeder::class,
        PbxUserSeeder::class,
        PbxPackageServerSeeder::class,
        PbxDidAndExtSeeder::class,
        InternationalRateSeeder::class,
        PbxServicesSeeder::class,
        PbxCDRSeeder::class,
    ]);

});

it('it process all the cdr', function () {

    $this->artisan('pbx:calculateCDRs');

    $service = PbxServices::first();
    $cdr = new CallDetailRegister();
    $cdr->setTable($cdr->firstOrCreateTableFromService($service));
    expect($cdr->count())->toBe(9276)
        ->and($cdr->whereNotNull('billed_at')->count())->toBe(9_276)
        ->and($cdr->where('cost', '>', 0)->count())->toBe(1_588)
        ->and($cdr->where('cost', '>', 0)->sum('billing_duration'))->toBe(247_911)
        ->and($cdr->sum('round_duration'))->toBe('247911')
        ->and($cdr->sum('exclusive_seconds'))->toBe('161211');
    //->and($cdr->sum('exclusive_cost'))->toBe(402.95384);// Por algún extra;o motivo la sumatoria da: 402.9538399999992
});

it('bring all item when using relationship', function () {

    expect(PbxServices::first()->customInboundItems()->count())->toBe(12)
        ->and(PbxServices::first()->customOutboundItems()->count())->toBe(13);

    extraItems();

    expect(PbxServices::first()->customInboundItems()->count())->toBe(14)
        ->and(PbxServices::first()->customOutboundItems()->count())->toBe(13);

});


it('working', function () {

    extraItems();

    $this->artisan('pbx:calculateCDRs');

    $service = PbxServices::first();
    $cdr = new CallDetailRegister();
    $cdr->setTable($cdr->firstOrCreateTableFromService($service));


    expect($cdr->count())->toBe(9276)
        ->and($cdr->whereNotNull('billed_at')->count())->toBe(9_276)
        ->and($cdr->where('cost', '>', 0)->count())->toBe(1_590)
        ->and($cdr->sum('exclusive_seconds'))->toBe('161262')
        ->and($cdr->sum('round_duration'))->toBe('247962')//247911 (51)
        ->and($cdr->sum('exclusive_cost'))->toBe(403.08134);

    //402.95384
    //247911

    //0.0935


});

function extraItems()
{
    $group = PrefixrateGroup::factory()->create(['type' => 'Inbound']);
    /* @var PrefixrateGroup $group */
    $service = PbxServices::first();
    $service->customGroups()->attach($group->id, ['type' => PbxServicePrefixRateGroup::TYPE_INBOUND]);

    $company = \Crater\Models\Company::first();

    $rate = \Crater\Models\CustomRate::create([
        'prefix' => 'Favour Ugochi (1111)',
        'rate_per_minute' => 0.11,
        "status" => "A",
        'company_id' => $company->id,
        'prefixrate_groups_id' => $group->id,
    ]);

    CustomRateGroupItems::create([
        'int_rate_id' => $rate->id,
        'prefixrate_id' => $group->id,
        'company_id' => $company->id,
    ]);

    $rate = \Crater\Models\CustomRate::create([
        'prefix' => '2532450714',
        'rate_per_minute' => 0.11,
        "status" => "A",
        'company_id' => $company->id,
        'prefixrate_groups_id' => $group->id,
    ]);

    CustomRateGroupItems::create([
        'int_rate_id' => $rate->id,
        'prefixrate_id' => $group->id,
        'company_id' => $company->id,
    ]);

    Log::debug([
        'customRate2' => PbxServices::first()->customInboundItems[13]->customRate,
        'customRate' => PbxServices::first()->customInboundItems[12]->customRate,

    ]);

}
