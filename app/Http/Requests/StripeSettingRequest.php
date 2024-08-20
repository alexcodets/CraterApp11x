<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StripeSettingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'public_key' => ['required'],
            'secret_key' => ['required'],
            'status' => ['required'],
            'currency' => ['required'],
            'environment' => ['required'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
