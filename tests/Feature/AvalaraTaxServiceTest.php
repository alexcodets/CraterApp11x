<?php

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
use Crater\Models\AvalaraConfig;
use Crater\Models\AvalaraLocation;
use Crater\Models\Company;
use Crater\Models\Invoice;
use Crater\Models\User;

beforeAll(function () {
});
beforeEach(function () {
    $this->config = AvalaraConfig::factory()->create();
    $this->service = new AvalaraService(new AvalaraApi($this->config));
});
//TODO: Test Manual And Invoice
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
