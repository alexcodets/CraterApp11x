<?php

use Crater\Models\BalanceCustomer;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\User;
use Crater\Services\Payment\BalancePaymentService;

it('Is Atomic', function () {
    $quantity = 100;
    $user = User::factory()->forCompany()->create();
    /* @var User $user */
    $invoice = Invoice::factory()->create(['user_id' => $user->id]);
    /* @var Invoice $invoice */

    $payment = new BalancePaymentService($user, $invoice);
    $response = [
        'paid' => 1000,
        'old_balance' => 15,
    ];
    Payment::unsetEventDispatcher();
    for ($i = 0; $i < $quantity; $i++) {
        $payment->handle($response);
    }

    expect(Payment::count())->toBe($quantity)
        ->and(Payment::distinct()->count('payment_number'))->toBe($quantity)
        ->and(Payment::distinct()->count('unique_hash'))->toBe($quantity)
        ->and(BalanceCustomer::count())->toBe($quantity)
        ->and(BalanceCustomer::first())->payment_id->toBe(Payment::first()->id);
});
