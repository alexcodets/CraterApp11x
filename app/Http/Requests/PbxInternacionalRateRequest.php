<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxInternacionalRateRequest extends FormRequest
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
        $rules = [
            /*'prefixrate_groups_id' => [
                'required'
            ],*/
            'country_id' => [
                'nullable',
                'integer'
            ],
            'status' => [
                'required'
            ],
            'category' => [
                'required'
            ],
        ];

        return $rules;
    }
}
