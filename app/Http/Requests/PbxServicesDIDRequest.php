<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PbxServicesDIDRequest extends FormRequest
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
                'integer'
            ],
            'creator_id' => [
                'nullable',
                'integer'
            ],
            'pbx_service_id' => [
                '',
                'integer'
            ],
            'pbx_did_id' => [
                '',
                'integer'
            ],
            /* 'pbx_profile_did_id' => [
                '',
                'integer'
            ], */

        ];
    }
}
