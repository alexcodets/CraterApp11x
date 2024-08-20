<?php

namespace Services\Payment\AuxVault;

use Artisan;
use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Crater\Models\AuxVault;
use Crater\Models\AuxVaultSetting;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentMethod;
use Crater\Models\User;
use Crater\Services\Payment\AuxVault\AuxVaultData;
use Crater\Services\Payment\AuxVault\AuxVaultPaymentService;
use Log;
use Omnipay\Common\Exception\InvalidCreditCardException;

//Test what happen when api_key or url is empty.

test('test credentials', function () {
    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
    //Log::debug($paymentAccount);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);
    AuxVaultSetting::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'endpoint' => config('services.aux_vault.url'),
        'api_key' => config('services.aux_vault.api_key'),
        'merchant_id' => config('services.aux_vault.merchant_id'),
        'default' => true,
    ]);

    $user = UserData::fromModel($user);

    $data = new AuxVaultData($paymentAccount, $user, 0);

    $response = AuxVaultPaymentService::handleCreditCard($data);

    expect($response)->amount->toBe(0.0)
    ->name->toBe($paymentAccount->first_name);

    //    expect($response)->status()->toBe(200);
    //    Log::debug($response->status());
    //    Log::debug($response->json());

});

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

    //Log::debug($userModel);
    CompanySetting::factory()->create([
        'option' => 'save_pdf_to_disk',
        'value' => 'NO',
        'company_id' => $user->company_id,
    ]);
    CompanySetting::factory()->create([
        'option' => 'allow_autodebit_customer_job',
        'value' => 1,
        'company_id' => $user->company_id,
    ]);

    CompanySetting::factory()->create([
        'option' => 'time_run_autodebit_customer_job',
        'value' => now()->format('H:i'),
        'company_id' => $user->company_id,
    ]);

    CustomerConfig::factory()->create([
        'customer_id' => $user->id,
        'enable_auto_debit' => 1,
        'company_id' => $user->company_id,
    ]);

    //aqui editar.
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'payment_account_type' => 'CC',
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    PaymentMethod::factory()->create([
        'status' => 'A',
        'account_accepted' => 'C',
    ]);

    PaymentGateways::factory()->create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'AuxVault',
    ]);

    AuxVaultSetting::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'endpoint' => config('services.aux_vault.url'),
        'api_key' => config('services.aux_vault.api_key'),
        'merchant_id' => config('services.aux_vault.merchant_id'),
        'default' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'due_amount' => 125,
        'sub_total' => 125,
    ]);

    Artisan::call('Invoice:Autodebit');

    $userModel->refresh();
    $invoice->refresh();
    $aux = AuxVault::first();
    Log::debug('invoice');
    Log::debug($invoice);
    Log::debug('---AuxModel-----');
    Log::debug(Payment::count());
    Log::debug(Payment::first());
    Log::debug($userModel->balance);
    Log::debug($aux);

    expect($invoice)->due_amount->toBe(125)
        ->and($aux)->amount->toBeNumeric(4.0)
        ->amount->toBe('4.00')
        ->and($userModel)->balance->toBe(4.0)
        ->and(AuxVault::count())->toBe(1)
        ->and(Payment::count())->toBe(1);

});

test('invoice payment with credit no recharge', function () {

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
            'balance' => 5,
        ]);
    $user = UserData::fromModel($userModel);
    Log::debug($userModel);
    CompanySetting::factory()->create([
        'option' => 'save_pdf_to_disk',
        'value' => 'NO',
        'company_id' => $user->company_id,
    ]);
    CompanySetting::factory()->create([
        'option' => 'allow_autodebit_customer_job',
        'value' => 1,
        'company_id' => $user->company_id,
    ]);

    CompanySetting::factory()->create([
        'option' => 'time_run_autodebit_customer_job',
        'value' => now()->format('H:i'),
        'company_id' => $user->company_id,
    ]);

    CustomerConfig::factory()->create([
        'customer_id' => $user->id,
        'enable_auto_debit' => 1,
        'auto_debit_days_before_due' => 3,
        'company_id' => $user->company_id,
    ]);

    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'payment_account_type' => 'CC',
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    PaymentMethod::factory()->create([
        'status' => 'A',
        'account_accepted' => 'C',
    ]);

    PaymentGateways::factory()->create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'AuxVault',
    ]);

    AuxVaultSetting::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'endpoint' => config('services.aux_vault.url'),
        'api_key' => config('services.aux_vault.api_key'),
        'merchant_id' => config('services.aux_vault.merchant_id'),
        'default' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'due_amount' => 125,
        'sub_total' => 125,
        'status' => 'DUE',
        'paid_status' => 'UNPAID',
        'not_charge_automatically' => 0,
        'due_date' => now()->addDays(3)->format('Y-m-d'),
    ]);

    Artisan::call('Invoice:Autodebit');

    $userModel->refresh();
    $invoice->refresh();

    expect($invoice)->due_amount->toBe(0)
        ->and($userModel)->balance->toBe(3.75)
        ->and(AuxVault::count())->toBe(0)
        ->and(Payment::count())->toBe(1);

});

test('invoice payment with credit and recharge', function () {

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
            'balance' => 4,
        ]);
    $user = UserData::fromModel($userModel);
    // 4 -> 2.75 -> 6.75

    Log::debug($userModel);
    CompanySetting::factory()->create([
        'option' => 'save_pdf_to_disk',
        'value' => 'NO',
        'company_id' => $user->company_id,
    ]);
    CompanySetting::factory()->create([
        'option' => 'allow_autodebit_customer_job',
        'value' => 1,
        'company_id' => $user->company_id,
    ]);

    CompanySetting::factory()->create([
        'option' => 'time_run_autodebit_customer_job',
        'value' => now()->format('H:i'),
        'company_id' => $user->company_id,
    ]);

    CustomerConfig::factory()->create([
        'customer_id' => $user->id,
        'enable_auto_debit' => 1,
        'auto_debit_days_before_due' => 3,
        'company_id' => $user->company_id,
    ]);

    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'payment_account_type' => 'CC',
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    PaymentMethod::factory()->create([
        'status' => 'A',
        'account_accepted' => 'C',
    ]);

    PaymentGateways::factory()->create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'AuxVault',
    ]);

    AuxVaultSetting::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'endpoint' => config('services.aux_vault.url'),
        'api_key' => config('services.aux_vault.api_key'),
        'merchant_id' => config('services.aux_vault.merchant_id'),
        'default' => true,
    ]);

    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'due_amount' => 125,
        'sub_total' => 125,
        'status' => 'DUE',
        'paid_status' => 'UNPAID',
        'not_charge_automatically' => 0,
        'due_date' => now()->addDays(3)->format('Y-m-d'),
    ]);

    Artisan::call('Invoice:Autodebit');

    $userModel->refresh();
    $invoice->refresh();
    Log::debug('invoice');
    Log::debug($invoice);
    Log::debug('---AuxModel-----');
    Log::debug(AuxVault::count());
    Log::debug(AuxVault::first());
    Log::debug('---Payment-----');
    Log::debug(Payment::count());
    Log::debug(Payment::first());
    Log::debug($user->balance);

    expect($invoice)->due_amount->toBe(0)
        ->and($userModel)->balance->toBe(6.75)
        ->and(AuxVault::count())->toBe(1)
        ->and(Payment::count())->toBe(2);

});

test('cannot use a old cc', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);

    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $userModel->id,
        'expiration_date' => \Crypt::encryptString(now()->subMonth()->format('Y-m')),
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new AuxVaultData($paymentAccount, $user, 500);

    AuxVaultPaymentService::handleCreditCard($data);
})->throws(InvalidCreditCardException::class, 'Card has expired');

test('expiration date is required', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'expiration_date' => null,
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new AuxVaultData($paymentAccount, $user, 500);

    $response = AuxVaultPaymentService::handleCreditCard($data);
})->throws(InvalidCreditCardException::class, 'The expiration month is required');


test('credit card number must be valid', function () {
    $userModel = User::factory()->create();
    $user = UserData::fromModel($userModel);
    $paymentAccount = PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'card_number' => \Crypt::encryptString('caraota'),
    ]);
    $paymentAccount = PaymentAccountData::fromModel($paymentAccount);

    $data = new AuxVaultData($paymentAccount, $user, 500);

    $response = AuxVaultPaymentService::handleCreditCard($data);
})->throws(InvalidCreditCardException::class, 'The credit card number is required');
