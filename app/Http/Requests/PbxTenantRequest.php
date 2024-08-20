<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxTenantRequest extends FormRequest
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
            'code' => [
                ''
            ],
            'details' => [
                ''
            ],
            'pbx_server_id' => [
                'nullable',
                'integer'
            ],

        ];
    }
}
