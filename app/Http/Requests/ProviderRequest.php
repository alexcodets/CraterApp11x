<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProviderRequest extends FormRequest
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
            'title' => [
                'required',
                'string',
                'unique:providers,title',
            ],
            'email' => [
                'required',
                'string',
                'unique:providers,email',
            ],
            'first_name' => [
                'nullable',
                'string',
            ],
            'last_name' => [
                'nullable',
                'string',
            ],
            'company' => [
                'nullable',
                'string',
            ],
            'country_id' => [
                'nullable',
                'integer',
            ],
            'state_id' => [
                'nullable',
                'integer',
            ],
            'city' => [
                'nullable',
                'string',
            ],
            'street' => [
                'nullable',
                'string',
            ],
            'zip_code' => [
                'nullable'
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'phone' => [
                'nullable',
                'string',
            ],
            'mobile' => [
                'nullable',
                'integer',
            ],

            'webside' => [
                'nullable',
                'string',
            ],
            'terms' => [
                'nullable',
                'string',
            ],
            'opening_balance' => [
                'nullable',
                'integer',
            ],

            'account_no' => [
                'nullable',
            ],
            'business_no' => [
                'nullable'
            ],
            'track_payments' => [
                'nullable',
                'boolean',
            ],
            'default_expense_account' => [
                'nullable',
                'string',
            ],
            'company_id' => [
                'nullable',
                'integer',
            ],
            'creator' => [
                'nullable',
                'integer',
            ],
            'status' => [
                'nullable',
                'string',
            ],
        ];

        if ($this->getMethod() == 'PUT') {
            $data['title'] = [
                'required',
                Rule::unique('providers')->ignore($this->route('provider'), 'id'),
            ];
            $data['email'] = [
                'required',
                Rule::unique('providers')->ignore($this->route('provider'), 'id'),
            ];
            $data['display_name'] = [
                'required',
                Rule::unique('providers')->ignore($this->route('provider'), 'id'),
            ];
        }

        return $data;
    }
}
