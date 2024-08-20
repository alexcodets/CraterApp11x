<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'name' => [
                'required'
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')
            ],
            'phone' => [
                'nullable'
            ],
            'password' => [
                'required',
                'min:8'
            ]
        ];

        if ($this->getMethod() == 'PUT') {
            $rules['email'] = [
                'required',
                'email',
                Rule::unique('users')->ignore($this->user)
            ];
            $rules['current_password'] = [
                'nullable',
                'min:8'
            ];
        }

        return $rules;
    }
}
