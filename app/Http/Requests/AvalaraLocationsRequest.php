<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AvalaraLocationsRequest extends FormRequest
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
            'addresses_id' => [
                'nullable',
                'integer'
            ],
            'county' => [
                'nullable',
                'string'
            ],
            'country' => [
                'nullable',
                'string'
            ],
            'state' => [
                'nullable',
                'string'
            ],
            'city' => [
                'nullable',
                'string'
            ],
            'address' => [
                'nullable',
                'string'
            ],
            'zip' => [
                'nullable',
                'string'
            ],
            'incorporated' => [
                'nullable',
                'integer'
            ],
            'pcd' => [
                'nullable',
                'string'
            ],
            'fips' => [
                'nullable',
                'string'
            ],
            'npa' => [
                'nullable',
                'string'
            ],
            'geo' => [
                'nullable',
                'integer'
            ],
            'type' => [
                'nullable',
                'integer'
            ],
            'user_id' => [
                'required',
            ]

        ];
    }

    public function avalaraLocationData()
    {
        return array_diff_key($this->validated(), array_flip(['active_user', 'active_company']));

    }
}
