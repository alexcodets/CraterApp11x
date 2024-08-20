<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FailedPaymentHistoryRequest extends FormRequest
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
        $data = [
            'payment_gateway' => [
                'required',
                'string',
            ],
            'transaction_number' => [
                'nullable',
                'string',
            ],
            'date' => [
                'required',
                'string'
            ],
            'amount' => [
                'required',
            ],
            'payment_number' => [
                'required',
                'string'
            ],
            'customer_id' => [
                'required',
                'integer'
            ],
            'invoice_id' => [
                'nullable',
                'integer'
            ],
            'description' => [
                'nullable',
                'string',
            ],
        ];

        return $data;
    }
}
