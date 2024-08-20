<?php

namespace Crater\Http\Requests;

use Crater\Models\User;
use Crater\Rules\UniqueNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
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
            'customcode' => [
                'required',
                new UniqueNumber(User::class)
            ],
            'name' => [
                'required'
            ],
            'addresses.*.address_street_1' => [
                'max:255'
            ],
            'addresses.*.address_street_2' => [
                'max:255'
            ],
            'email' => [
                'email',
                'nullable',
                'unique:users,email',
            ],
            'status_payment' => [
                'required'
            ]
        ];

        if ($this->isMethod('PUT') && $this->email != null) {
            $rules = [
                'customcode' => [
                    'required',
                    new UniqueNumber(User::class, $this->id)
                ],
                'email' => [
                    'email',
                    'nullable',
                    Rule::unique('users')->ignore($this->route('customer')->id)
                ]
            ];
        };

        return $rules;
    }
}
