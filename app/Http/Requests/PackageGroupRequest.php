<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PackageGroupRequest extends FormRequest
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
                'unique:package_groups,name'
            ],
            'description' => [
                'nullable',
                'string'
            ],
            'allow_upgrades' => [
                'required',
                'boolean'
            ]
        ];

        if ($this->getMethod() == 'PUT') {
            $data['name'] = [
                'required',
                Rule::unique('package_groups')->ignore($this->route('group'), 'id')
            ];
        }

        return $data;
    }
}
