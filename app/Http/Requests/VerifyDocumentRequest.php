<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyDocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cambia esto según tus necesidades de autorización
    }

    public function rules(): array
    {
        return [
            'ic_front' => 'required|string',
            'ic_back' => 'required|string',
            'selfie' => 'required|string',
        ];
    }
}
