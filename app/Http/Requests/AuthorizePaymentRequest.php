<?php

namespace Crater\Http\Requests;

class AuthorizePaymentRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['nullable', 'int'],
            'name' => ['required', 'min:3'],
            'company_name' => ['nullable', 'min:3'],
            'amount' => ['required', 'int', 'min:100'],
            'date' => ['required'],
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
            'main_account' => ['nullable', 'string'],
            'payer_id' => ['nullable', 'int'],
            'payer_company_id' => ['nullable', 'int'],
            'payment_gateway_id' => ['nullable', 'int'],
            'nameOnAccount' => ['required', 'string', 'min:3', 'max:21' ],
            'has_fees' => ['nullable', 'bool'],
            'fees' => ['nullable', 'array', 'required_if:has_fees,1'],
            'fees.*' => ['integer'],
        ];

    }

    public function authorize(): bool
    {
        return true;
    }
}
