<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentMethodRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $data = [
            'name' => [
                'required',
                'unique:payment_methods,name'
            ],
            'status' => [
                'nullable',
            ],
            'only_cash' => [
                'boolean',
            ],
            'add_payment_gateway' => [
                'nullable',
            ],
            'paypal_button' => [
                'boolean',
            ],
            'stripe_button' => [
                'boolean',
            ],
            'for_customer_use' => [
                'nullable',
            ],
            'generate_expense' => [
                'nullable',
            ],
            'account_accepted' => [
                'nullable',
            ],
            'payment_gateways_id' => [
                'nullable',
            ],
            'void_refund' => [
                'nullable'
            ],
            'generate_expense_id' => [
                'nullable'
            ],
            'void_refund_expense_id' => [
                'nullable'
            ],
            'expense_import' => [
                'nullable'
            ],
            'show_notes_table' => [
                'nullable'
            ],
        ];

        if ($this->getMethod() == 'PUT') {
            $data['name'] = [
                'required',
                Rule::unique('payment_methods')->ignore($this->route('payment_method'), 'id')
            ];
        }

        return $data;
    }
}
