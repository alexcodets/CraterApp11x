<?php

namespace Crater\Services\Payment;

use Crater\Helpers\General;
use Crater\Models\BalanceCustomer;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\User;
use Crater\Traits\PaymentTrait;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class BalancePaymentService
{
    use PaymentTrait;

    private User $user;

    private Invoice $invoice;

    public function __construct(User $user, Invoice $invoice)
    {
        $this->user = $user;
        $this->invoice = $invoice;
    }

    public function handle(array $totals): Payment
    {
        Log::debug('*** Payment for Balance ***');
        $payment = Cache::lock('payment_generate', 10)->block(12, function () use ($totals) {
            return Payment::create([
                'payment_date' => now()->format('Y-m-d'),
                'amount' => $totals["paid"],
                'user_id' => $this->user->id,
                'company_id' => $this->user->company_id,
                'invoice_id' => $this->invoice->id,
                'payment_number' => $this->freshPaymentNumber(),
                'notes' => 'Auto debit Invoice Payment with Balance',
                'unique_hash' => General::generateUniqueId(),
            ]);
        });
        /* @var Payment $payment */
        \Log::debug("Payment {$payment->id} created from BalancePaymentService: with payment_number: {$payment->payment_number}");

        $this->newBalance($totals, $payment);

        return $payment;

    }

    private function newBalance(array $totals, Payment $payment)
    {
        BalanceCustomer::create([
            'status' => 'D',
            'present_balance' => $totals['old_balance'],
            'amount_op' => $totals['paid'] / 100,
            'amount_final' => $this->user->balance,
            'payment_id' => $payment->id,
            'user_id' => $this->user->id,
        ]);
    }
}
