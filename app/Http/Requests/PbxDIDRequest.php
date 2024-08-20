<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxDIDRequest extends FormRequest
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
            'pbx_tenant_id' => [
                'integer'
            ],
            'number' => [
                ''
            ],
            'server' => [
                ''
            ],
            'status' => [
                ''
            ],
            'api_id' => [
                ''
            ],
            'e164' => [
                'nullable',
                ''
            ],
            'e164_2' => [
                'nullable',
                ''
            ],
            'ext' => [
                'nullable',
                ''
            ],
            'number2' => [
                'nullable',
                ''
            ],
            'type' => [
                'nullable',
                ''
            ],
            'trunk' => [
                'nullable',
                ''
            ]
        ];
    }
}
