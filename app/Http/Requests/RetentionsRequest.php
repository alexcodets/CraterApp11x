<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RetentionsRequest extends FormRequest
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
            'concept' => [
                'required', 'string'
            ],
            'minimium_base_per_unit' => [
                'required', 'integer'
            ],
            'minimium_base_in_currency' => [
                'required', 'integer'
            ],
            'type_of_minimium_base_in_currency' => [
                'required', 'string'
            ],
            'percentage' => [
                'required', 'numeric'
            ],
            'foreing' => [
                'required', 'string'
            ],
            'country_id' => [
                'required', 'integer'
            ],
            /* 'type_vat_regime' => [
                'integer'
            ], */
            'great_contributor' => [
                'required', 'boolean'
            ],
            'self_retaining' => [
                'required', 'boolean'
            ],
            'vat_withholding_agent' => [
                'required', 'boolean'
            ],
            'simple_tax_regime' => [
                'required', 'boolean'
            ]

        ];
    }
}
