<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
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
            'expense_number' => [
                'required'
            ],
            'expense_date' => [
                'required'
            ],
            'expense_category_id' => [
                'required'
            ],
            'amount' => [
                'required'
            ],
            'user_id' => [
                'nullable'
            ],
            'notes' => [
                'nullable'
            ],
            'providers_id' => [
                'nullable'
            ],
            'items_id' => [
                'nullable'
            ],
            'status' => [
                'required'
            ],
            'subject' => [
                'required'
            ],
            'notification' => [
                'nullable'
            ],
            'payment_method_id' => [
                'nullable'
            ],
            'payment_date' => [
                'nullable'
            ],
            'store_id' => [
                'nullable'
            ],
        ];
    }
}
