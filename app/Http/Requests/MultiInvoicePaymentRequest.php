<?php

namespace Crater\Http\Requests;

use Crater\Models\Invoice;

class MultiInvoicePaymentRequest extends BaseApiRequest
{
    public function rules(): array
    {
        //Invoice::whereIn('id', ) auth()->user()->invoices()->sum('due_amount')

        $rules = [
            'invoices' => ['bail', 'required', 'array'],
            'invoices.*' => ['bail', 'required', 'integer', 'in:'.implode(',', auth('sanctum')->user()->invoices()->pluck('id')->toArray())],
            'amount' => ['bail', 'required', 'numeric', 'min:100'],
            'user_id' => ['nullable', 'int'],
            'name' => ['nullable', 'min:3'],
            'company_name' => ['nullable', 'min:3'],
            'date' => ['nullable'],
            'invoice_number' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'payment_number' => ['nullable', 'string'],
            'custom_code' => ['nullable', 'string'],
            'address_street_1' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'payment_method_id' => ['nullable', 'int'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'country_id' => ['nullable', 'int'],
            'state_id' => ['nullable', 'int'],
            'city' => ['nullable', 'string'],
            'email' => ['nullable', 'string'],
            'address_1' => ['nullable', 'string'],
            'address_2' => ['nullable', 'string'],
            'zip' => ['nullable', 'string'],
            'payment_account_type' => ['nullable', 'string'],
            'card_number' => ['nullable', 'string'],
            'credit_card' => ['nullable', 'string'],
            'cvv' => ['nullable', 'string'],
            'expiration_date' => ['nullable', 'string'],
            'ACH_type' => ['nullable', 'string'],
            'account_number' => ['nullable', 'string'],
            'routing_number' => ['nullable', 'string'],
            'num_check' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'company_id' => ['nullable', 'int'],
            'created_at' => ['nullable', 'string'],
            'bank_name' => ['nullable', 'string'],
            'main_account' => ['nullable', 'bool'],
            'payer_id' => ['nullable', 'int'],
            'payer_company_id' => ['nullable', 'int'],
            'payment_gateway_id' => ['nullable', 'int'],
            'nameOnAccount' => ['nullable', 'string', 'min:3', 'max:21'],
            'has_fees' => ['nullable', 'bool'],
            'fees' => ['nullable', 'array', 'required_if:has_fees,1'],
            'fees.*' => ['integer'],
        ];

        if (auth('sanctum')->user()->role == 'super admin') {
            $rules['invoices.*'] = ['bail', 'required', 'integer'];
            $rules['amount'] = ['bail', 'required', 'numeric', 'min:100'];
        }

        return $rules;
    }

    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'invoices.*.in' => 'The id :input is not a valid invoice Id',
            'amount.max' => 'The amount (:input) may not be greater than the total of the invoices, :max.',
        ];
    }
}
