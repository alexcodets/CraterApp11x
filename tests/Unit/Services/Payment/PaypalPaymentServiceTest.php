<?php

namespace Services\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Crater\Models\Company;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentGateways;
use Crater\Models\PaypalSetting;
use Crater\Models\User;
use Crater\Services\Payment\Paypal\PaymentPaypalData;
use Crater\Services\Payment\Paypal\PaypalPaymentService;
use Log;
use Omnipay\Common\Exception\InvalidCreditCardException;

test('amount cannot be zero', function () {
    //Error with name = VALIDATION_ERROR
    $user = User::factory()
        ->for(Company::factory())
        ->create();
    PaypalSetting::factory()->create([
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
    $service = PaypalPaymentService::handleCreditCard($paypalDO);
    Log::debug('Service: ', $service);

    expect($service['success'])->toBeFalse()
        ->and($service['payment_gateway'])->toBe('paypal')
        ->and($service['message'])->toBe('- Amount cannot be zero');


    //$response = AuxVaultPaymentService::handleCreditCard($data, 'model');
});

test('generic error, fail', function () {
    //standard errors?
    $user = User::factory()
        ->for(Company::factory())
        ->create();
    PaypalSetting::factory()->create([
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

    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 100);
    $service = PaypalPaymentService::handleCreditCard($paypalDO);
    Log::debug('Service: ', $service);

    expect($service['success'])->toBeFalse()
        ->and($service['payment_gateway'])->toBe('paypal')
        ->and($service['message'])->toBe('Payee account is invalid.');

    //$response = AuxVaultPaymentService::handleCreditCard($data, 'model');
});

test('CC expiration date cannot be in the past', function () {
    //standard errors?
    $user = User::factory()
        ->for(Company::factory())
        ->create();
    PaypalSetting::factory()->create([
        'creator_id' => $user->id,
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'expiration_date' => now()->subMonth()->format('Y-m')
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    $user = UserData::fromModel($user);

    $paypalDO = new PaymentPaypalData($paymentAccount, $user, 100);
    $service = PaypalPaymentService::handleCreditCard($paypalDO);


    //$response = AuxVaultPaymentService::handleCreditCard($data, 'model');
})->throws(InvalidCreditCardException::class, 'Card has expired');
