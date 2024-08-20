<?php

namespace Crater\Http\Requests;

use Crater\Models\Invoice;
use Crater\Rules\UniqueNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoicesRequest extends FormRequest
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
     * Get the validation rules that apply to the request.s
     *
     * @return array
     */
    public function rules(): array
    {
        $data = $this->request->all();
        $rules = [
            'invoice_date' => [
                'required'
            ],
            'due_date' => [
                'required'
            ],
            'user_id' => [
                'required'
            ],
            'invoice_number' => [
                'required',
                new UniqueNumber(Invoice::class)
            ],
            'discount' => [
                'required'
            ],
            'discount_val' => [
                'required'
            ],
            'sub_total' => [
                'required'
            ],
            'total' => [
                'required'
            ],
            'tax' => [
                'required'
            ],
            'invoice_template_id' => [
                'required'
            ],
            'items' => [
                Rule::requiredIf($data['banType']),
                'array'
            ],
            'items.*' => [
                Rule::requiredIf($data['banType']),
                'max:255'
            ],
            'items.*.description' => [
                'max:255'
            ],
            'items.*.name' => [
                Rule::requiredIf($data['banType']),
            ],
            'items.*.quantity' => [
                Rule::requiredIf($data['banType']),
            ],
            'items.*.price' => [
                Rule::requiredIf($data['banType']),
            ]
        ];

        if ($this->isMethod('PUT')) {
            $rules['invoice_number'] = [
                'required',
                new UniqueNumber(Invoice::class, $this->route('invoice')->id)
            ];
        }

        return $rules;
    }
}
