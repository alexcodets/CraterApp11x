<?php

namespace Crater\Http\Requests;

class PaymentVoidRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            // Indica si el void debe aplicar solo a local.
            'only_local' => ['nullable', 'bool']
            //'transaction_status' => ['required', 'in:Void']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
