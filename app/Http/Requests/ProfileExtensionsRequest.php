<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileExtensionsRequest extends FormRequest
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
                'nullable',
            ],
            'unmetered' => [
                'nullable',
                'boolean'
            ],
            'rate' => [
                ''
            ],
            'minutes_cap' => [
                'nullable'
            ],
            'minutes_increments' => [
                'nullable'
            ],
            'type_time_increment' => [
                'nullable', 'string'
            ],
            'outbound_per_minute_rate' => [
                'nullable',
                'numeric'
            ],
            'inbound_per_minute_rate' => [
                'nullable',
                'numeric'
            ],
            'extension_balance' => [
                'nullable',
                'numeric'
            ],
            'minimum_extension_balance' => [
                'nullable',
                'numeric'
            ],
            'status_payment' => [
                'nullable',
                'string'
            ],
            'status' => [
                '',
                'string'
            ],
            'aditional_charges' => [
                ''
            ],
        ];
    }
}
