<?php

namespace Crater\Http\Requests;

class CheckAuxVaultSettingRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'endpoint' => ['required'],
            'api_key' => ['required'],
            'merchant_id' => ['required'],
            'currency' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
