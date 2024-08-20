<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentAccountRequest extends FormRequest
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
            'first_name' => [
                'required',
                'string'
            ],
            'country_id' => [
                'required',
                'integer'
            ],
            'state_id' => [
                'required',
                'integer'
            ],
            'city' => [
                'required',
                'string'
            ],
            'address_1' => [
                'required',
                'string'
            ],
            'address_2' => [
                'nullable',
                'string'
            ],
            'zip' => [
                'required'
            ],
            'payment_account_type' => [
                'required',
                'string'
            ],
            'card_number' => [
                'nullable',
                'string'
            ],
            'cvv' => [
                'nullable',
                'string'
            ],
            'expiration_date' => [
                'nullable',
                'string'
            ],
            'ACH_type' => [
                'nullable',
                'string'
            ],
            'account_number' => [
                'nullable',
                'string'
            ],
            'routing_number' => [
                'nullable',
                'string'
            ],
            'bank_name' => [
                'nullable',
                'string'
            ],
            'client_id' => [
                'required',
                'integer'
            ],
            'status' => [
                'nullable'
            ],
        ];

        if ($this->getMethod() == 'PUT') {
            $data['first_name'] = [
                'required',
            ];
            $data['address_1'] = [
                'required',
            ];
            $data['city'] = [
                'required',
            ];
            $data['state_id'] = [
                'required',
            ];
            $data['country_id'] = [
                'required',
            ];
            $data['zip'] = [
                'required',
            ];
            $data['payment_account_type'] = [
                'required',
            ];
        }

        return $data;
    }
}
