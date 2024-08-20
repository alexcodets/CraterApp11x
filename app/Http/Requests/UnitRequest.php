<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnitRequest extends FormRequest
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
                'unique:units,name'
            ]
        ];

        if ($this->getMethod() == 'PUT') {
            $data['name'] = [
                'required',
                Rule::unique('units')->ignore($this->route('unit'), 'id')
            ];
        }

        return $data;
    }
}
