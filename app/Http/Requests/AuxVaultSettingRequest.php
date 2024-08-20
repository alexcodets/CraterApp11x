<?php

namespace Crater\Http\Requests;

class AuxVaultSettingRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'endpoint' => ['required', 'string', 'between:5,200'],
            'name' => ['required', 'string'],
            'api_key' => ['required', 'string'],
            'merchant_id' => ['nullable'],
            'currency' => ['nullable'],
            'default' => ['boolean', 'boolean'],
            'production' => ['boolean', 'boolean'],
            'enable_identification_verification' => ['boolean', 'boolean'],
            'enable_fee_charges' => ['boolean', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
