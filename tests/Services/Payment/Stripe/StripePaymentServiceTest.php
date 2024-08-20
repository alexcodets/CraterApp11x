<?php

namespace Tests\Services\Payment\Stripe;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Crater\Models\{AuxVault,Company,Currency,Invoice,Payment,PaymentAccount,PaymentGateways,PaymentMethod,StripeSetting,User};
use Crater\Services\Payment\Stripe\Exceptions\StripeConfigurationModelNotFoundException;
use Crater\Services\Payment\Stripe\StripeData;
use Crater\Services\Payment\Stripe\StripePaymentService;
use DesolatorMagno\AuthorizePhp\Util\Log;
use Omnipay\Common\Exception\InvalidCreditCardException;

function generateSettings(UserData $user)
{
    StripeSetting::factory()->create([
        'public_key' => config('services.stripe_test.key'),
        'secret_key' => config('services.stripe_test.secret'),
        'creator_id' => $user->id,
    ]);
}

test('credit card number must be valid', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);
    generateSettings($user);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'card_number' => \Crypt::encryptString('caraota'),
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new StripeData($paymentAccount, $user, 500);

    $response = StripePaymentService::handleCreditCard($data);
})->throws(InvalidCreditCardException::class, 'The credit card number is required');

test('cannot use a old cc', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);
    generateSettings($user);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $userModel->id,
        'expiration_date' => \Crypt::encryptString(now()->subMonth()->format('Y-m')),
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new StripeData($paymentAccount, $user, 500);

    StripePaymentService::handleCreditCard($data);
})->throws(InvalidCreditCardException::class, 'Card has expired');

test('expiration date is required', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);
    generateSettings($user);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'expiration_date' => null,
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new StripeData($paymentAccount, $user, 500);

    $response = StripePaymentService::handleCreditCard($data);

})->throws(InvalidCreditCardException::class, 'The expiration month is required');

it('requires a stripe configuration', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);
    //$this->generateSettings($user);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'expiration_date' => null,
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new StripeData($paymentAccount, $user, 500);

    $response = StripePaymentService::handleCreditCard($data);

})->throws(StripeConfigurationModelNotFoundException::class, 'No Stripe Configuration found');

test('recharge credit works', function () {

    $userModel = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
        ]);
    $user = UserData::fromModel($userModel);
    generateSettings($user);

    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'payment_account_type' => 'CC',
        'card_number' => \Crypt::encryptString('4242424242424242'),
        'credit_card' => 'Homero',
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    PaymentMethod::factory()->create([
        'status' => 'A',
        'account_accepted' => 'C',
    ]);

    //Add Payment Gateway
    PaymentGateways::factory()->create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Stripe',
    ]);

    //    $invoice = Invoice::factory()->create([
    //        'user_id'    => $user->id,
    //        'company_id' => $user->company_id,
    //        'due_amount' => 125,
    //        'sub_total'  => 125,
    //    ]);

    //    expect($invoice)->due_amount->toBe(125)
    //        ->and($aux)->amount->toBeNumeric(4.0)
    //        ->amount->toBe('4.00')
    //        ->and($userModel)->balance->toBe(4.0)
    //        ->and(AuxVault::count())->toBe(1)
    //        ->and(Payment::count())->toBe(1);

    $data = new StripeData($paymentAccount, $user, 500);
    $response = StripePaymentService::handleCreditCard($data);
    Log::debug('Response: ', $response);

});

//TODO: Test, minimun amount (0.50)$ or 500.
