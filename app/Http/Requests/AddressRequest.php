<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
        return [
            'name' => [
                'string',
                'nullable'
            ],
            'address_street_1' => [
                'string',
                'nullable'
            ],
            'address_street_2' => [
                'string',
                'nullable'
            ],
            'city' => [
                'string',
                'nullable'
            ],
            'state_id' => [
                'integer',
                'nullable'
            ],
            'country_id' => [
                'integer',
                'nullable'
            ],
            'county' => [
                'string',
                'nullable'
            ],
            'zip' => [
                'string',
                'nullable'
            ],
            'phone' => [
                'string',
                'nullable'
            ],
            'fax' => [
                'string',
                'nullable'
            ],
            'type' => [
                'string',
                'nullable'
            ],
            'tax_exempt' => [
                'integer',
                'nullable'
            ],
            'delivery_method' => [
                'string',
                'nullable'
            ],
            'payment_notices' => [
                'integer',
                'nullable'
            ],
            'tax_id_vatin' => [
                'string',
                'nullable'
            ],
            'user_id' => [
                'integer',
                'nullable'
            ],
            'company_id' => [
                'integer',
                'nullable'
            ],
            'pcode' => [
                'integer',
                'nullable'
            ],
            'tax_agency_id' => [
                'integer',
                'nullable'
            ]
        ];
    }
}
