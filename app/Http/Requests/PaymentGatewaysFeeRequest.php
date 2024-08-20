<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentGatewaysFeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return  true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required'
            ],
            'type' => [
                'required'
            ],
            'amount' => [
                'required'
            ],
            'payment_gateway' => [
                'required'
            ],
            'authorize_setting_id' => [
                'nullable'
            ],
            'aux_vault_setting_id' => [
                'nullable'
            ],
            'paypal_settings_id' => [
                'nullable'
            ]
        ];
    }
}
