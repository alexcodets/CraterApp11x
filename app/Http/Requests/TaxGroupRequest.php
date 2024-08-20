<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxGroupRequest extends FormRequest
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
            'name' => [
                'required',
                'string',
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'status' => [
                'nullable',
                'string'
            ],
            'country_id' => [
                'nullable',
                'integer'
            ],
            'state_id' => [
                'nullable',
                'integer'
            ],
            'taxes' => [
                'required',
            ],
        ];

        if ($this->getMethod() == 'PUT') {
            $data['name'] = [
                'required',
                Rule::unique('tax_groups')->ignore($this->route('tax_group'), 'id')
            ];
        }

        return $data;
    }
}
