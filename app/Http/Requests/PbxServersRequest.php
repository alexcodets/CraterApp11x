<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxServersRequest extends FormRequest
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
            'company_id' => [
               'exists:companies,id'
            ],
            'creator_id' => [
                'nullable',
                'integer'
            ],
            'api_key' => [
                'nullable',
                'string'
            ],
            'country_id' => [
                'nullable',
                'integer'
            ],
            'hostname' => [
                'nullable',
                'string'
            ],
            'international_dialing_code' => [
                'nullable',
                'string'
            ],
            'national_dialing_code' => [
                'nullable',
                'string'
            ],
            'server_label' => [
                'nullable',
                'string'
            ],
            'ssl_port' => [
                'nullable',
                'string'
            ],
            'status' => [
                'nullable',
                'string'
            ],
            'timezone' => [
                'nullable',
                'string'
            ],
        ];
    }
}
