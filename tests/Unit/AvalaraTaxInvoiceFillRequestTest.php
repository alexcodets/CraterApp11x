<?php

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
use Crater\Models\AvalaraConfig;
use Crater\Models\AvalaraLocation;
use Crater\Models\Company;
use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\User;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->config = AvalaraConfig::factory()->create();
    $this->service = new AvalaraService(new AvalaraApi($this->config));
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    $taxService = new AvalaraTaxService($user, $this->service);

    /* @var Invoice $invoice */
    $this->invoice = Invoice::factory()->avalaraInvoice()
        ->has(InvoiceItem::factory()->avalaraValid()->count(3), 'items')
        ->create(['company_id' => $user->company_id, 'user_id' => $user->id]);

    //$invoice = Invoice::factory()->avalaraInvoice()->has(InvoiceItem::factory()->avalaraValid()->count(3), 'items')->create(['company_id' => 1, 'user_id' => 2]);

});

test('Invoice Works', function () {
    Http::fake();
    $response = $this->artisan('pbx:generateAvalaraTax', ['invoice_id' => $this->invoice->id]);
    expect($response)->toBeBool()->toBeFalse();
})->skip();

test('It Works', function () {

    /* @var AvalaraConfig $config */
    $config = AvalaraConfig::factory()->create();
    $this->service = new AvalaraService(new AvalaraApi($config));
    /* @var User $user */
    $user = User::factory()->for(Company::factory())->for(AvalaraLocation::factory(), 'location')->create();
    /* @var Invoice $invoice */
    $invoice = Invoice::factory()->avalaraInvoice()
        ->has(InvoiceItem::factory()->avalaraValid()->count(5), 'items')
        ->create(['company_id' => $user->company_id, 'user_id' => $user->id]);

    $taxService = new AvalaraTaxService($user, $this->service);

    expect($taxService->validateUserData($config, $invoice, $commit = true))
        ->success->toBeTrue()
        ->message->toBeNull();

    $invoiceItems = $this->invoice->items()->whereHas('item', function ($query) {
        $query->where('avalara_bool', '!=', null);
    })->with('item')->get();

    foreach ($invoiceItems as $invoiceItem) {
        $taxService->addTaxItemForService($invoiceItem->item, $invoiceItem);
        $invoiceItem->invoice_item_id = $invoiceItem->id;
    }


})->skip();
