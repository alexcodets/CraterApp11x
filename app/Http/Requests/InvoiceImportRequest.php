<?php

namespace Crater\Http\Requests;

class InvoiceImportRequest extends BaseApiRequest
{
    public function rules(): array
    {
        // file = .csv
        return [
            'file' => ['required', 'file', 'mimes:csv,txt'],
            'date_format' => ['nullable', 'string']
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
