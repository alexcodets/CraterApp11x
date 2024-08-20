<?php

namespace Tests\Services\Payment\Paypal;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Crater\Models\Company;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentGateways;
use Crater\Models\PaypalSetting;
use Crater\Models\User;
use Crater\Services\Payment\Exceptions\ActiveSettingException;
use Crater\Services\Payment\Paypal\PaymentPaypalData;
use Crater\Services\Payment\Paypal\PaypalService;
use Omnipay\Omnipay;

test('amount cannot be zero', function () {

    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $settings = PaypalSetting::factory()->create([
        'creator_id' => $user->id,
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    $user = UserData::fromModel($user);

    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 0);

    $service = new PaypalService($settings);
    $response = $service->paymentCreditCard($paypalDO);

    expect($response['success'])->toBeFalse()
        ->and($response['payment_gateway'])->toBe('paypal')
        ->and($response['message'])->toBe('- Amount cannot be zero');

});
test('amount cannot be to low', function () {

    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $settings = PaypalSetting::factory()->create([
        'creator_id' => $user->id,
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    $user = UserData::fromModel($user);

    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 10);

    $service = new PaypalService($settings);
    $response = $service->paymentCreditCard($paypalDO);

    expect($response['success'])->toBeFalse()
        ->and($response['payment_gateway'])->toBe('paypal')
        ->and($response['message'])->toBe('- Amount cannot be zero');

});

test('active paypal setting is required', function () {

    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $settings = PaypalSetting::factory()->create([
        'creator_id' => $user->id,
        'status' => 'I',
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    $user = UserData::fromModel($user);

    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 0);
    $service = new PaypalService();

})->throws(ActiveSettingException::class, 'Active paypal setting is required');
test('a invalid paypal setting can be used', function () {

    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $settings = PaypalSetting::factory()->create([
        'creator_id' => $user->id,
        'status' => 'I',
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    $user = UserData::fromModel($user);


    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 0);

    $service = new PaypalService($settings);
    $response = $service->paymentCreditCard($paypalDO);

    expect($response['success'])->toBeFalse()
        ->and($response['payment_gateway'])->toBe('paypal')
        ->and($response['message'])->toBe('- Amount cannot be zero');

});

test('list', function () {

    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $settings = PaypalSetting::factory()->create([
        'creator_id' => $user->id,
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    $user = UserData::fromModel($user);

    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 0);

    $gateway = Omnipay::create('PayPal_Rest');
    $gateway->initialize([
        'clientId' => $settings->public_key,
        'secret' => $settings->private_key,
        'testMode' => $settings->enviroment == 'sandbox', // Or false when you are ready for live transactions
    ]);

    $transaction = $gateway->listPurchase();

    \Log::debug('Response: ', $transaction->getData());


});
