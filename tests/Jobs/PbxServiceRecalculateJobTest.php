<?php

use Crater\Jobs\PbxServiceRecalculateJob;
use Crater\Models\AdditionalCharge;
use Crater\Models\Company;
use Crater\Models\ItemGroup;
use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesAppRate;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxServicesItems;
use Crater\Models\PbxServicesTaxTypes;
use Crater\Models\PbxTenant;
use Crater\Models\ProfileDID;
use Crater\Models\Tax;
use Crater\Models\User;
use Illuminate\Support\Facades\Cache;

/* @return array{0: User, 1: PbxServers, 2: PbxServices} */
function base(): array
{
    $user = User::factory()
        ->for(Company::factory(), 'company')
        ->create();

    $server = PbxServers::factory()->create();
    $profile = ProfileDID::factory()->create([
        'did_rate' => 0.25,
        'name' => 'did_',
        'company_id' => $user->company_id,
        'did_number' => '123456',
    ]);
    $package = PbxPackages::factory()->create([
        'rate_per_minutes' => null,
        'minutes_increments' => 0,
        'status' => 'I',
        'call_ratings' => 0,
        'pbx_server_id' => $server->id,
        'template_did_id' => $profile->id,
        'did' => 1,
    ]);
    $pbxService = PbxServices::factory()->create([
        'customer_id' => $user->id,
        'company_id' => $user->company_id,
        'pbx_package_id' => $package->id,
        'total' => 0,
        'sub_total' => 0,
        'tax' => 0,
    ]);
    Cache::put('tenant_synchronize', [$pbxService->id], 3600);

    return [$user, $server, $pbxService];

}

test('pbxServices with extensions and did', function () {

    [$user, $server, $pbxService] = base();

    $did = PbxDID::factory()->create([
        'company_id' => $user->company_id,
    ]);
    PbxServicesDID::factory()->create([
        'company_id' => $user->company_id,
        'pbx_service_id' => $pbxService->id,
        'cost_per_day' => 0.32,
        'price' => 0.32,
        'pbx_did_id' => $did->id,
    ]);

    $ext = PbxExtensions::factory()->create([
        'company_id' => $user->company_id,
    ]);
    PbxServicesExtensions::factory()->create([
        'company_id' => $user->company_id,
        'pbx_service_id' => $pbxService->id,
        'cost_per_day' => 0.15,
        'price' => 0.15,
        'pbx_extension_id' => $ext->id,
    ]);

    $serverTenant = PbxServerTenant::factory()
        ->create([
            'pbx_server_id' => $server->id,
            'company_id' => $user->company_id,
        ]);

    PbxTenant::factory()->create([
        'pbx_server_id' => $server->id,
        'company_id' => $user->company_id,
        'code' => $serverTenant->tenant_code,
        'tenantid' => $serverTenant->tenant_id,
    ]);

    Log::debug('Data Before: ', $pbxService->toArray());
    $total = 15 + 32;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($total);
});

test('pbxServices with app rate', function () {

    $pbxService = base()[2];

    PbxServicesAppRate::factory()->create([
        'quantity' => 3,
        'costo' => 15,
        'pbx_service_id' => $pbxService->id,
    ]);

    Log::debug('Data Before: ', $pbxService->toArray());
    $total = 1500;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($total);
});

test('pbxServices with additional charges', function () {

    $pbxService = base()[2];

    AdditionalCharge::factory()->create([
        'amount' => 3,
        'total' => 18,
        'pbx_service_id' => $pbxService->id,
    ]);

    Log::debug('Data Before: ', $pbxService->toArray());
    $total = 1800;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($total);
});

test('pbx services with items', function () {
    [$user, , $pbxService] = base();

    ItemGroup::factory()->create();
    $total = 23;
    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $total,
        'total' => $total,
    ]);

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($total);

});

test('pbx services with items and discount D percentage', function () {
    [$user, , $pbxService] = base();

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'D';
    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'percentage';
    $pbxService->allow_discount_value = 15;
    $pbxService->save();

    ItemGroup::factory()->create();

    $subTotal = 2300;
    $discount = (int)($subTotal * .15);
    $total = max(($subTotal - $discount), 0);

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});
test('Discount D fixed on days, in range will count', function () {
    [$user, , $pbxService] = base();

    $discountValue = 15;

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'D';
    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'fixed';
    $pbxService->allow_discount_value = $discountValue;
    $pbxService->save();

    ItemGroup::factory()->create();

    $subTotal = 2300;
    $discount = ($discountValue * 100);
    $total = max(($subTotal - $discount), 0);

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});

test('Discount fixed T on Days, outside of range wont calculate', function () {
    [$user, , $pbxService] = base();

    $discountValue = 15;

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'T';
    $pbxService->time_period_value = 'Days';
    $pbxService->time_period = now()->diffInDays(now()->startOfMonth()) - 1;
    $pbxService->date_begin = now()->startOfMonth();

    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'fixed';
    $pbxService->allow_discount_value = $discountValue;
    $pbxService->save();

    ItemGroup::factory()->create();

    $subTotal = 2300;
    $total = $subTotal;

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});
test('Discount fixed T on Days, in range will work', function () {
    [$user, , $pbxService] = base();

    $discountValue = 15;

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'T';
    $pbxService->time_period_value = 'Days';
    $pbxService->time_period = now()->diffInDays(now()->startOfMonth()) + 1;
    $pbxService->date_begin = now()->startOfMonth();

    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'fixed';
    $pbxService->allow_discount_value = $discountValue;
    $pbxService->save();

    ItemGroup::factory()->create();

    $subTotal = 2300;
    $discount = ($discountValue * 100);
    //$discount = $discount * (now()->diffInDays($pbxService->date_begin));
    $total = max(($subTotal - $discount), 0);

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});

test('Discount percentage T on Days, in range will work', function () {
    [$user, , $pbxService] = base();

    $discountValue = 15;

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'T';
    $pbxService->time_period_value = 'Days';
    $pbxService->time_period = now()->diffInDays(now()->startOfMonth()) + 1;
    $pbxService->date_begin = now()->startOfMonth();

    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'percentage';
    $pbxService->allow_discount_value = $discountValue;
    $pbxService->save();

    ItemGroup::factory()->create();

    $subTotal = 2300;
    $discount = (int)($subTotal * .15);
    //$discount = $discount * (now()->diffInDays($pbxService->date_begin));
    $total = max(($subTotal - $discount), 0);

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});

test('pbx services with items and discount another', function () {
    [$user, , $pbxService] = base();

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'D';
    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'percentage';
    $pbxService->allow_discount_value = 13;
    $pbxService->save();

    ItemGroup::factory()->create();

    $subTotal = 250;
    $discount = (int)($subTotal * .13);
    $total = max(($subTotal - $discount), 0);

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});

test('total cannot be zero', function () {
    [$user, , $pbxService] = base();

    $pbxService->allow_discount = 1;
    $pbxService->discount_term_type = 'D';
    $pbxService->date_to = now()->addDays(3);
    $pbxService->date_from = now()->subDays(3);
    $pbxService->allow_discount_type = 'percentage';
    $pbxService->allow_discount_value = 115;
    $pbxService->save();

    ItemGroup::factory()->create();
    $subTotal = 23;
    $discount = (int)(23 * 1.15);
    $total = max(($subTotal - $discount), 0);

    PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $subTotal,
        'total' => $subTotal,
    ]);

    PbxServiceRecalculateJob::dispatchSync([$pbxService->id]);

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($subTotal);

});

test('pbx services with items and item tax', function () {
    [$user, , $pbxService] = base();

    PbxServices::where('id', $pbxService->id)->update([
        'apply_tax_type' => 'I',
    ]);
    $base = 23;
    ItemGroup::factory()->create();

    $item = PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $base,
        'total' => $base,
    ]);

    Tax::factory()
        ->times(3)
        ->sequence(['amount' => 100], ['amount' => 120], ['amount' => 140])
        ->create([
            'pbx_service_item_id' => $item->id,
        ]);

    $tax = 100 + 120 + 140;
    $total = $base + $tax;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)
        ->total->toBe($total)
        ->sub_total->toBe($base)
        ->tax->toBe($tax);

});

test('pbx services with items, will ignore item taxes if type differ from I', function () {
    [$user, , $pbxService] = base();

    PbxServices::where('id', $pbxService->id)->update([
        'apply_tax_type' => 'G',
    ]);
    $base = 23;
    ItemGroup::factory()->create();

    $item = PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $base,
        'total' => $base,
    ]);

    Tax::factory()
        ->times(3)
        ->sequence(['amount' => 100], ['amount' => 120], ['amount' => 140])
        ->create([
            'pbx_service_item_id' => $item->id,
        ]);

    //$tax = 100 + 120 + 140;
    //$total = $base + $tax;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($base)->sub_total->toBe($base)->tax->toBe(0);

});

test('pbx services with items, will process service tax', function () {
    [$user, , $pbxService] = base();

    PbxServices::where('id', $pbxService->id)->update([
        'apply_tax_type' => 'G',
    ]);
    $base = 23;
    ItemGroup::factory()->create();

    $item = PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $base,
        'total' => $base,
    ]);

    PbxServicesTaxTypes::factory()->create([
        'pbx_services_id' => $pbxService->id,
        'percent' => 9,
    ]);

    Tax::factory()
        ->times(3)
        ->sequence(['amount' => 100], ['amount' => 120], ['amount' => 140])
        ->create([
            'pbx_service_item_id' => $item->id,
        ]);

    //$tax = 100 + 120 + 140;
    //$total = $base + $tax;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($base + 2)->sub_total->toBe($base)->tax->toBe(2);

});

test('tax type G will ignore item tax', function () {
    [$user, , $pbxService] = base();

    $itemPrice = 30;

    PbxServicesTaxTypes::factory()->create([
        'pbx_services_id' => $pbxService->id,
        'percent' => 9,
    ]);

    ItemGroup::factory()->create();

    $item = PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $itemPrice,
        'total' => $itemPrice,
    ]);

    Tax::factory()
        ->times(3)
        ->sequence(['amount' => 100], ['amount' => 120], ['amount' => 140])
        ->create([
            'pbx_service_item_id' => $item->id,
        ]);

    $serviceTax = (int)round(($itemPrice * 0.09));
    $total = $itemPrice + $serviceTax;

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    expect($service)->total->toBe($total)
        ->sub_total->toBe($itemPrice)
        ->tax->toBe($serviceTax);

});

test('tax type I, with only items will ignore normal tax', function () {
    [$user, , $pbxService] = base();

    PbxServices::where('id', $pbxService->id)->update([
        'apply_tax_type' => 'I',
    ]);
    $itemPrice = 30;
    ItemGroup::factory()->create();

    $item = PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $itemPrice,
        'total' => $itemPrice,
    ]);

    PbxServicesTaxTypes::factory()->create([
        'pbx_services_id' => $pbxService->id,
        'percent' => 9,
    ]);

    Tax::factory()
        ->times(3)
        ->sequence(['amount' => 2], ['amount' => 3], ['amount' => 4])
        ->create([
            'pbx_service_item_id' => $item->id,
        ]);

    $itemTax = 2 + 3 + 4;
    //$itemTax = (int) round(($itemPrice * 0.09),0);

    PbxServiceRecalculateJob::dispatchSync();

    $service = PbxServices::first();

    Log::debug('Data After: ', $service->toArray());

    //(2 will come from 0.09%  of items(23))

    expect($service)->total->toBe($itemPrice + $itemTax)
        ->sub_total->toBe($itemPrice)
        ->tax->toBe($itemTax);

});

test('tax type I works with both type of tax', function () {
    [$user, , $pbxService] = base();

    PbxServices::where('id', $pbxService->id)->update([
        'apply_tax_type' => 'I',
    ]);
    $itemPrice = 30;
    $packagePrice = 20;

    $pbxService->pbxpackages_price = $packagePrice;
    $pbxService->save();
    ItemGroup::factory()->create();

    $item = PbxServicesItems::factory()->create([
        'company_id' => $user->company_id,
        'pbx_services_id' => $pbxService->id,
        'price' => $itemPrice,
        'total' => $itemPrice,
    ]);

    PbxServicesTaxTypes::factory()->create([
        'pbx_services_id' => $pbxService->id,
        'percent' => 9,
    ]);

    Tax::factory()
        ->times(3)
        ->sequence(['amount' => 2], ['amount' => 3], ['amount' => 4])
        ->create([
            'pbx_service_item_id' => $item->id,
        ]);

    $itemTax = 2 + 3 + 4;
    $serviceTax = ($packagePrice * 100 * 0.09);

    PbxServiceRecalculateJob::dispatchSync();
    $service = PbxServices::first();
    Log::debug('Data After: ', $service->toArray());

    //(2 will come from 0.09%  of items(23))
    Log::debug(
        'Totals: ',
        [
            'package_total' => ($packagePrice * 100),
            'package_tax' => $serviceTax,
            'item_total' => $itemPrice,
            'item_tax' => $itemTax,
        ]
    );

    $totalTax = (int)($itemTax + $serviceTax);
    $total = (int)(($packagePrice * 100) + $serviceTax + $itemPrice + $itemTax);
    //2000 + 30 + 180 + 9
    Log::debug('Total: '.$total);
    //"sub_total":2030,"total":2219,"tax":189

    expect($service)->total->toBe($total)
        ->sub_total->toBe($itemPrice + ($packagePrice * 100))
        ->tax->toBe($totalTax);

});



// TODO:
// Se apliquen los descuentos.
// EL monto total no puede ser 0 (al aplicar descuentos).
// item tax y service tax van por separados???
// test it can be run manually.
//\Crater\Traits\PbxServicesReCalculateTrait::calculatePriceService($pbxService, false);
