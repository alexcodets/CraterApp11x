<?php

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
use Crater\Models\Address;
use Crater\Models\AvalaraConfig;
use Crater\Models\AvalaraLocation;
use Crater\Models\Company;
use Crater\Models\Country;
use Crater\Models\Invoice;
use Crater\Models\State;
use Crater\Models\User;

beforeAll(function () {
});
beforeEach(function () {
    $this->config = AvalaraConfig::factory()->create();
    $this->service = new AvalaraService(new AvalaraApi($this->config));
});

uses()->group('Avalara', 'AvalaraTaxService');

//Test for address/Location

test('The tax services requires a valid Billing Address/Location', function ($user, $text) {
    /* @var User $user */
    //Log::debug($user);
    $taxService = new AvalaraTaxService($user, $this->service);

    /* @var Invoice $invoice */
    $invoice = Invoice::factory()->avalaraInvoice()->create(['company_id' => $user->company_id, 'user_id' => $user->id]);
    $validation = $taxService->validateUserData($this->config, $invoice, $commit = true);

    expect($validation)
        ->message->toBeString()->toBe($text)
        ->success->toBeFalse();

})->with(
    [
        'No address' => [
            fn () => User::factory()->for(Company::factory())->create(),
            fn () => __('avalara.error.location.address.required.model')
        ],
        'Shipping address' => [
            fn () => User::factory()->for(Company::factory())->has(Address::factory()->shipping())->create(),
            fn () => __('avalara.error.location.address.required.model')
        ],
        'No state' => [
            fn () => User::factory()->for(Company::factory())->has(Address::factory()->billing())->create(),
            fn () => __('avalara.error.location.address.required.state')
        ],
        'No Country' => [
            fn () => User::factory()->for(Company::factory())->has(Address::factory()->billing()->for(State::factory()))->create(),
            fn () => __('avalara.error.location.address.required.country')
        ],

        'Company Bill Address' => [
            fn () => User::factory()->for(Company::factory()->has(Address::factory()->billing()->for(State::factory())->for(Country::factory())))->create(),
            fn () => __('avalara.error.location.address.required.model')
        ],

        'Valid Bill Address' => [
            fn () => User::factory()->for(Company::factory())->has(Address::factory()->billing()->for(State::factory())->for(Country::factory()))->create(),
            fn () => __('avalara.error.item.required.id')
        ],

        'Valid User Location' => [
            fn () => User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create(),
            fn () => __('avalara.error.item.required.id')
        ],

        'Valid Company Location' => [
            fn () => User::factory()->for(Company::factory()->for(AvalaraLocation::factory(), 'location'))->create(),
            fn () => __('avalara.error.item.required.id')
        ],

    ]
);

test('Avalara Configuration missing', function ($array, $text) {
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    $taxService = new AvalaraTaxService($user, $this->service);

    $this->config->update($array);

    /* @var Invoice $invoice */
    $invoice = Invoice::factory()->avalaraInvoice()->create(['company_id' => $user->company_id, 'user_id' => $user->id]);
    $validation = $taxService->validateUserData($this->config, $invoice, $commit = true);
    expect($validation)
        ->message->toBeString()->toBe($text)
        ->success->toBeFalse();

})->with(
    [
        'Bussines class' => [
            fn () => ['bscl' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'Service class' => [
            fn () => ['svcl' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'facilities class' => [
            fn () => ['fclt' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'Regulated' => [
            fn () => ['reg' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'Franchise' => [
            fn () => ['svcl' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'Identifier' => [
            fn () => ['company_identifier' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'Account Reference' => [
            fn () => ['account_reference' => null],
            fn () => __('avalara.error.company_model.required.base')
        ],
        'Item CDR' => [
            fn () => [],
            fn () => __('avalara.error.company_model.items.required.cdr')
        ],
        'Item DID' => [
            fn () => ['item_cdr_id' => 1],
            fn () => __('avalara.error.company_model.items.required.did')
        ],
        'Item Ext' => [
            fn () => ['item_cdr_id' => 1, 'item_did_id' => 1],
            fn () => __('avalara.error.company_model.items.required.ext')
        ],
    ]
);


test('It pass the validation', function () {
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    $taxService = new AvalaraTaxService($user, $this->service);

    /* @var Invoice $invoice */
    $invoice = Invoice::factory()->avalaraInvoice()->create(
        ['company_id' => $user->company_id, 'user_id' => $user->id, 'invoice_date' => '09001728888']
    );

    $this->config->update(['item_cdr_id' => 1, 'item_did_id' => 1, 'item_extension_id' => 1]);

    $validation = $taxService->validateUserData($this->config, $invoice, $commit = true);
    expect($validation)
        ->success->toBeTrue()
        ->message->toBeNull();

});

test('A invoice tax can not be calculated if one or more LineItem are invalid', function () {
    //----------Preparation--------
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();

    //Log::debug($user);
    //$user = User::factory()->hasCompany(Company::factory()->create())->create();

    $taxService = new AvalaraTaxService($user, $this->service);
    /* @var Invoice $invoice */
    $invoice = Invoice::factory()->avalaraInvoice()->create(['company_id' => $user->company_id, 'user_id' => $user->id]);
    $validation = $taxService->validateUserData($this->config, $invoice, $commit = true);

    echo 'Inside test';

    if ($validation['success'] === false) {
        //Or whatever logic for failing.
        echo 'false';
        echo $validation['message'];

        return false;
    }

    if ($validation['success']) {
        echo 'true';

        return false;
    }


    /*    $doc = '12345';
        $invoice = new AvalaraInvoiceDO('2018-09-24 11:00:00', false, $doc);
        $billing = new AvalaraDataBillingDO('USA', 'NC', 'Durham', 27701);
        $company = new AvalaraCompanyDataDO(1, 1, true, true, true);

        $this->service->prepareTax($invoice, $billing, $company);
        $this->service->addLine(5, 19, 21, 1); //line VOIP
        $this->service->addLine(2, 19, 578, 1); //pbx VOIP
        $this->service->addLine(4, 19, 41, 1); //pbx_extension VOIP
        $this->service->addCharge(19.99, 19, 8, 1); //install VOIP
        $this->service->addCharge(29.99, 19, 48, 100); //call VOIP (wrong sale number)
        $response = $this->service->getTaxes();

        $this->assertSame($response['status'], -2000);
        $this->assertFalse($response['success']);
        $this->assertSame($response['message'], 'One or more LineItems are invalid.');*/
})->skip();

dataset('valid invoice', function () {
    yield fn () => Invoice::factory()->create([
        'pbx_service_id' => 1,
        'user_id' => 2,
        'company_id' => 1,
        'pbx_total_items' => 5,
        'pbx_total_extension' => 10,
        'pbx_total_did' => 5,
        'pbx_total_aditional_charges' => 0,
        'pbx_total_cdr' => 25,
        'sub_total' => 125,
        'total' => 125,
        'discount' => 0,
        'discount_val' => 0,
        'invoice_template_id' => 3,
        'invoice_date' => now()->subDays(2)->format('Y-m-d'),
        'due_date' => now()->addDays(20)->format('Y-m-d'),
    ]);
});
