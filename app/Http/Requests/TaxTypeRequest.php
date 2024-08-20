<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxTypeRequest extends FormRequest
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
                'required'
            ],
            'percent' => [
                'required'
            ],
            'description' => [
                'nullable'
            ],
            'compound_tax' => [
                'nullable'
            ],
            'collective_tax' => [
                'nullable'
            ],
            'state_id' => [
                'nullable'
            ],
            'country_id' => [
                'nullable'
            ],
            'tax_category_id' => [
                'nullable'
            ],
            'tax_agency_id' => [
                'nullable'
            ]
        ];
    }
}
