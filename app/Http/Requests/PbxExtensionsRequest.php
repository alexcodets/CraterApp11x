<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxExtensionsRequest extends FormRequest
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
                'nullable',
            ],
            'creator_id' => [
                'nullable',
                'integer'
            ],
            'name' => [
                ''
            ],
            'email' => [
                ''
            ],
            'status' => [
                ''
            ],
            'pbx_tenant_id' => [
                'integer'
            ],
            'api_id' => [
                'nullable',
                ''
            ],
            'ext' => [
                'nullable',
                ''
            ],
            'linenum' => [
                'nullable',
                ''
            ],
            'location' => [
                'nullable',
                ''
            ],
            'macaddress' => [
                'nullable',
                ''
            ],
            'protocol' => [
                'nullable',
                ''
            ],
            'ua_fullname' => [
                'nullable',
                ''
            ],
            'ua_id' => [
                'nullable',
                ''
            ],
            'ua_name' => [
                'nullable',
                ''
            ]
        ];
    }
}
