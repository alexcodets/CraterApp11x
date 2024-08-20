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
use Crater\Models\Item;
use Crater\Models\State;
use Crater\Models\User;

beforeAll(function () {
});
beforeEach(function () {
    $this->config = AvalaraConfig::factory()->create();
    $this->service = new AvalaraService(new AvalaraApi($this->config));
});

uses()->group('Avalara', 'AvalaraTaxService');
//validateUserData
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

test('It pass the User Data Validation', function () {
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
//validateItemData
test('Items is not a Avalara Item', function (Item $item, string $text) {
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    $taxService = new AvalaraTaxService($user, $this->service);

    $validation = $taxService->validateItemData($item);
    expect($validation)
        ->success->toBeFalse()
        ->message->toBeString()->toBe($text);
})->with(
    [
        'Null_avalara_bool' => [
            fn () => Item::factory()->avalara()->create(['avalara_bool' => null]),
            fn () => __('avalara.tax_services.error.avalara_bool.null')
        ],
        'Zero_avalara_bool' => [
            fn () => Item::factory()->avalara()->create(['avalara_bool' => 0]),
            fn () => __('avalara.tax_services.error.avalara_bool.null')
        ],
        'Null_avalara_payment' => [
            fn () => Item::factory()->avalara()->create(['avalara_payment_type' => null]),
            fn () => __('avalara.tax_services.error.payment_type.null')
        ],
        'Invalid_avalara_payment' => [
            fn () => Item::factory()->avalara()->create(['avalara_payment_type' => 'NOTHING?']),
            fn () => __('avalara.tax_services.error.payment_type.invalid')
        ],
        'Null_service_type' => [
            fn () => Item::factory()->avalara()->create(['avalara_service_type' => null, 'id' => 5]),
            fn () => __('avalara.tax_services.error.service_type.null', ['item' => 5])
        ],
        'Invalid_service_type' => [
            fn () => Item::factory()->avalara()->create(['avalara_service_type' => 2, 'id' => 5]),
            fn () => __('avalara.tax_services.error.service_type.invalid', ['item' => 5, 'id' => 2])
        ],
        'null_avalara_type' => [
            fn () => Item::factory()->avalara()->create(['avalara_type' => null, 'id' => 5]),
            fn () => __('avalara.tax_services.error.avalara_type.null', ['item' => 5])
        ],
    ]
);

test('It pass the Avalara Item Validation', function () {
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    $taxService = new AvalaraTaxService($user, $this->service);

    /* @var Item $item */
    $item = Item::factory()->avalara()->create();

    $validation = $taxService->validateItemData($item);
    expect($validation)
        ->success->toBeTrue()
        ->message->toBeNull();
});

//test('it only add Avalara Items')->skip();
test('it add Avalara Items Type Line and Amount', function () {
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    $taxService = new AvalaraTaxService($user, $this->service);

    /* @var Invoice $invoice */
    $invoice = Invoice::factory()->avalaraInvoice()->create(
        ['company_id' => $user->company_id, 'user_id' => $user->id, 'invoice_date' => '09001728888']
    );

    $this->config->update(['item_cdr_id' => 1, 'item_did_id' => 1, 'item_extension_id' => 1]);
    $taxService->validateUserData($this->config, $invoice, $commit = true);

    /* @var Item $item */
    $item = Item::factory()->avalara()->create();
    $request = (object)[
        'total' => 525,
        'quantity' => 13,
    ];

    $taxService->addTaxItemForService($item, $request);
    $body = $taxService->service->request->body;
    $itm = $body['inv'][0]['itms'][0];

    expect($itm)
        ->chg->toBe($request->total / 100)
        ->line->toBe(0)
        ->tran->toBe($item->avalaraServiceType->avalara_transaction_types)
        ->serv->toBe($item->avalaraServiceType->service_type)
        ->ref->toBe($item->description ?? $item->name);

    $item = Item::factory()->avalara()->create(['avalara_payment_type' => 'LINES']);
    $request = (object)[
        'total' => 725,
        'quantity' => 14,
    ];

    $taxService->addTaxItemForService($item, $request);
    $body = $taxService->service->request->body;

    expect($body['inv'][0]['itms'][1])
        ->chg->toBe(0)
        ->line->toBe($request->quantity)
        ->tran->toBe($item->avalaraServiceType->avalara_transaction_types)
        ->serv->toBe($item->avalaraServiceType->service_type)
        ->ref->toBe($item->description ?? $item->name);

});

//test('It add Exemption')->skip();
//test('It add Bundle')->skip();
