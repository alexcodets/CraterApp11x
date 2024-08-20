<?php

namespace Services\Payment;

use Crater\DataObject\PaymentAccountData;
use Crater\DataObject\UserData;
use Crater\Models\AuxVault;
use Crater\Models\AuxVaultSetting;
use Crater\Models\Company;
use Crater\Models\PaymentAccount;
use Crater\Models\User;
use Crater\Services\Payment\AuxVault\AuxVaultData;
use Crater\Services\Payment\AuxVault\AuxVaultPaymentService;

#AuxVault
//Test what happen when api_key or url is empty.
test('test credentials', function () {
    $user = User::factory()
        ->for(Company::factory())
        ->create();
    $paymentAccount = PaymentAccount::factory()->create(['client_id' => $user->id]);
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

    $response = AuxVaultPaymentService::handleCreditCard($data, 'model');

    expect($response)->amount->toBe(0.0)
        ->name->toBe($paymentAccount->first_name);

});
