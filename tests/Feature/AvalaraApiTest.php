<?php

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\DataObject\AvalaraCompanyDataDO;
use Crater\Avalara\DataObject\AvalaraDataBillingDO;
use Crater\Avalara\DataObject\AvalaraInvoiceDO;
use Crater\Avalara\Service\AvalaraService;
use Crater\Avalara\Service\AvalaraTaxService;
use Crater\Models\AvalaraConfig;
use Crater\Models\User;

beforeAll(function () {
});
beforeEach(function () {
    $this->config = AvalaraConfig::factory()->createOne();
    /* @var AvalaraConfig $this->config  */
    $this->service = new AvalaraService(new AvalaraApi($this->config));
    $this->url = 'communicationsua.avalara.net/api/v2';
});

uses()->group('api');

test('Server is up and running', function () {
    Http::fake([
        'communicationsua.avalara.net/api/v2/HealthCheck' => Http::response(
            [
                'Status' => 'Healthy'
            ],
            200,
            []
        ),
    ]);
    $response = $this->service->serverStatus();
    expect($response)->status->toBe(200)
        ->message->toBe(__('exception.curl.28'));
})->group('api');

test('Core Server is down cant access avalara server', function () {
    $errorMessage = 'cURL error 28: Resolving timed out after 4000 milliseconds (see https://curl.haxx.se/libcurl/c/libcurl-errors.html) for https://communicationsua.avalara.net/api/v2/HealthCheck';
    /*Http::fake([
        $this->url . '/HealthCheck' => Http::response(
            [
                'error' => $errorMessage
            ], 500, []),
    ]);*/
    Http::fake()->timeout(0);
    $response = $this->service->serverStatus();
    expect($response)->status->toBe(500)
    ->message->toBe(__('exception.curl.28'));
})->group('api');


test('it existes', function () {
    $this->assertNotEmpty($this->config);
})->group('api');

test('A invoice tax can not be calculated if one or more LineItem are invalid', function () {
    $user = User::factory()->createOne();
    /* @var User $user */
    //create Invoice
    $taxService = new AvalaraTaxService($user, $this->service);
    $validation = $taxService->validateUserData($this->config, null, $commit = true);

    /*Http::fake([
        $this->url . '/HealthCheck' => Http::response(
            [
                'error' => $errorMessage
            ], 500, []),
    ]);*/

    if ($validation['success'] === false) {
        //Or whatever logic for failing.
        Log::debug('Early exit?');
        Log::debug($validation);

        return false;
    }

    $doc = '12345';
    $invoice = new AvalaraInvoiceDO('2018-09-24 11:00:00', false, $doc);
    $billing = new AvalaraDataBillingDO('USA', 'NC', 'Durham', 27701);
    $company = new AvalaraCompanyDataDO(1, 1, true, true, true);

    $this->service->prepareTax($invoice, $billing, $company);
    $this->service->addLine(5, 19, 21, 1, 'Line VOIP'); //line VOIP
    $this->service->addLine(2, 19, 578, 1, 'PBX VOIP'); //pbx VOIP
    $this->service->addLine(4, 19, 41, 1, 'PBX Extension VOIP'); //pbx_extension VOIP
    $this->service->addCharge(19.99, 19, 8, 1, 'Install VOIP'); //install VOIP
    $this->service->addCharge(29.99, 19, 48, 100, 'CALL VOIP (WRONG)'); //call VOIP (wrong sale number)
    $response = $this->service->getTaxes();

    $this->assertSame($response['status'], -2000);
    $this->assertFalse($response['success']);
    $this->assertSame($response['message'], 'One or more LineItems are invalid.');
});

test('A invoice tax can be calculated', function () {
    $doc = '12345';
    $invoice = new AvalaraInvoiceDO('2018-09-24 11:00:00', false, $doc);
    $billing = new AvalaraDataBillingDO('USA', 'NC', 'Durham', 27701);
    $company = new AvalaraCompanyDataDO(1, 1, true, true, true);

    $this->service->prepareTax($invoice, $billing, $company);
    $this->service->addLine(5, 19, 21, 1, 'Line VOIP'); //line VOIP
    $this->service->addLine(2, 19, 578, 1, 'PBX VOIP'); //pbx VOIP
    $this->service->addLine(4, 19, 41, 1, 'PBX Extension VOIP'); //pbx_extension VOIP
    $this->service->addCharge(19.99, 19, 8, 1, 'Install VOIP'); //install VOIP
    $this->service->addCharge(29.99, 19, 48, 1);
    'Call VOIP'; //call VOIP
    $response = $this->service->getTaxes();

    $this->assertSame($response['status'], 200);
    $this->assertTrue($response['success']);
    $this->assertSame($response['data']['doc'], $doc);
});

test('A wrong invoice id can not be commited', function () {
    $doc = '12345'.'amp123pma';
    $response = $this->service->CommitInvoice($doc);
    $this->assertFalse($response['success']);
});

test('A wrong invoice id can not be unCommited', function () {
    $doc = '12345'.'amp123pma';
    $response = $this->service->unCommitInvoice($doc);
    $this->assertFalse($response['success']);
});

test('A invoice tax can be commited', function () {
    $doc = '12345';
    $response = $this->service->CommitInvoice($doc);
    $this->assertSame($response['status'], 200);
    $this->assertTrue($response['success']);
    $this->assertSame($response['message'], __('avalara.success.commit'));
});

test('A invoice tax can be Uncommited', function () {
    $doc = '12345';
    $response = $this->service->unCommitInvoice($doc);
    $this->assertSame($response['status'], 200);
    $this->assertTrue($response['success']);
    $this->assertSame($response['message'], __('avalara.success.uncommit'));
});
