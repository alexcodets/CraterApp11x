<?php

use Crater\DataObject\PaymentDO;
use Crater\Models\Country;
use Crater\Models\Payment;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentMethod;
use Crater\Models\State;
use Crater\Models\User;

it('is Atomic', function () {
    $quantity = 100;
    $user = User::factory()->forCompany()->create();
    /* @var User $user */

    $meto = PaymentMethod::factory()->create([
        'company_id' => $user->company_id,
    ]);
    /* @var PaymentMethod $meto */

    $account = PaymentAccount::Factory()->create([
        'client_id' => $user->id,
        'company_id' => $user->company_id,
        'payment_account_type' => 'CC',
        'country_id' => Country::factory()->create()->id,
        'state_id' => State::factory()->create()->id,
        'main_account' => 1,
    ]);

    /* @var PaymentAccount $account */

    $response = [
        'note' => 'notar',
        'payment_method_id' => $meto->id,
    ];

    $payment = new PaymentDO($account, $user, 500);
    Payment::unsetEventDispatcher();
    for ($i = 0; $i < $quantity; $i++) {
        $payment->generateSuccessPayment($response);
    }
    Payment::distinct()->count('payment_number');
    expect(Payment::count())->toBe($quantity)
        ->and(Payment::distinct()->count('payment_number'))->toBe($quantity)
        ->and(Payment::distinct()->count('unique_hash'))->toBe($quantity);

});
