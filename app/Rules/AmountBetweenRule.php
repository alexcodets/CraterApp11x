<?php

namespace Crater\Rules;

use Crater\Models\Payment;
use Illuminate\Contracts\Validation\Rule;

class AmountBetweenRule implements Rule
{
    private Payment $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function passes($attribute, $value): bool
    {
        return $value > 0 && $value <= $this->payment->amount;
    }

    public function message(): string
    {
        return 'The amount is invalid.';
    }
}
