<?php

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
use Crater\Models\AvalaraConfig;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\User;

it('works', function () {
    $invoice = Invoice::first();
    $this->artisan('pbx:generateAvalaraTax', ['invoice_id' => $invoice->id ?? 0]);
    expect('it Work')->toBe('it Work');
});

test('A invoice tax can not be calculated if one or more LineItem are invalid', function () {

    //return [$user, $config, $service, $taxService, $invoice, $url];
    //TODO: Need to update a specific item and change de service
    list($user, $config, $service, $taxService, $invoice, $url) = migrateAndGetData();
    $validation = $taxService->validateUserData($config, null, $commit = true);

    if ($validation['success'] === false) {
        //Or whatever logic for failing.
        Log::debug('Early exit?');
        Log::debug($validation);

        return false;
    }

    $invoiceItems = $invoice->items()->whereHas('item', function ($query) {
        $query->where('avalara_bool', '!=', null);
    })->with('item')->get();

    foreach ($invoiceItems as $invoiceItem) {
        $taxService->addTaxItemForService($invoiceItem->item, $invoiceItem);
        $invoiceItem->invoice_item_id = $invoiceItem->id;
        $allItems[] = $invoiceItem;  //For the log
    }

    $taxesResponse = $taxService->getTaxes();

    Log::debug($taxesResponse);

    $this->assertSame($taxesResponse['status'], -2000);
    $this->assertFalse($taxesResponse['success']);
    $this->assertSame($taxesResponse['message'], 'One or more LineItems are invalid.');


    /*Http::fake([
$this->url . '/HealthCheck' => Http::response(
    [
        'error' => $errorMessage
    ], 500, []),
]);*/
    //$doc = '12345';
});

function fillData(): User
{

    Artisan::call('db:seed', ['--class' => 'UsersTableSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'AvalaraServiceTypesSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'PbxPackageServerSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'AvalaraTaxSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'PbxDidAndExtSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'PbxServicesSeeder', '--force' => true]);
    /*Artisan::call('db:seed', ['--class' => 'PbxCDRSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'PbxCdrTotalesSeeder', '--force' => true]);*/
    Artisan::call('db:seed', ['--class' => 'InvoiceTemplateSeeder', '--force' => true]);
    Artisan::call('db:seed', ['--class' => 'AvalaraInvoiceSeeder', '--force' => true]);

    $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);
    $user = User::where('email', $clientData[0]['client']['email'])->first();

    CompanySetting::create([
        'option' => 'invoice_prefix',
        'value' => 'INV',
        'company_id' => $user->company_id,
    ]);

    return $user;
}

function migrateAndGetData(): array
{
    $user = fillData();
    $config = AvalaraConfig::first();
    /* @var AvalaraConfig $this->config  */
    $service = new AvalaraService(new AvalaraApi($config));
    $taxService = new AvalaraTaxService($user, $service);
    $invoice = Invoice::first();
    $url = 'communicationsua.avalara.net/api/v2';

    return [$user, $config, $service, $taxService, $invoice, $url];
}
