<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxAgencyRequest extends FormRequest
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
            'number' => [
                ''
            ],
            'email' => [
                'nullable'
            ],
            'phone' => [
                'nullable'
            ],
            'website' => [
                'nullable'
            ],
            'note' => [
                'nullable'
            ],
            /* 'country_id' => [
                'nullable'
            ],
            'tax_category_id' => [
                'nullable'
            ] */
        ];
    }
}
