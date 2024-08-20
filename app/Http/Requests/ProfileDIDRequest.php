<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileDIDRequest extends FormRequest
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
            'description' => [
                ''
            ],
            'status' => [
                ''
            ],
            'did_rate' => [
                'nullable',
                'numeric'
            ],
            'toll_free_did_rate' => [
                'nullable',
                'numeric'
            ],
            'international_did_rate' => [
                'nullable',
                'numeric'
            ],
            'international_inbound_per_minute_rate' => [
                'nullable',
                'numeric'
            ],
            'inbound_per_minute_rate' => [
                'nullable',
                'boolean'
            ],
            'emergency_services_rate' => [
                'nullable',
                'boolean'
            ],
            'inbound_per_minute_rate_value' => [
                'nullable',
                'numeric'
            ],
            'emergency_services_rate_value' => [
                'nullable',
                'numeric'
            ],
            'emergency_services_address' => [
                'nullable',
                'string'
            ],
            'emergency_services_city' => [
                'nullable',
                'string'
            ],
            'emergency_services_state' => [
                'nullable',
                'string'
            ],
            'emergency_services_zip' => [
                'nullable',
                'string'
            ],
            'cnam_rate' => [
                'nullable',
                'boolean'
            ],
            'unmetered' => [
                'nullable',
                'boolean'
            ],
            'cnam_rate_value' => [
                'nullable',
                'numeric'
            ],
            'cnam_name' => [
                'nullable',
                'string'
            ],
            'cnam_price' => [
                'nullable',
                'numeric'
            ],
            'aditional_charges' => [
                ''
            ],
        ];
    }
}
