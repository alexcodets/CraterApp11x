<?php

namespace Crater\Http\Requests;

use Crater\Rules\AmountBetweenRule;

class PaymentRefundRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:partial,total'],
            'amount' => ['nullable', 'numeric', new AmountBetweenRule($this->payment)],
            'note' => ['nullable', 'string'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
