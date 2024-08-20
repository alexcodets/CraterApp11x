<?php

namespace Crater\Http\Requests;

class CustomerServiceIndexRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'status' => ['nullable', 'in:A,P,S,C'],
            'user_id' => ['nullable', 'int'],
            'date' => ['nullable', 'in:created,renewal,begin,activation_date'],
            'date_begin' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }

    protected function passedValidation(): void
    {
        switch ($this->date) {
            case 'created':
                $this->merge(['date' => 'created_at']);

                break;
            case 'renewal':
                $this->merge(['date' => 'renewal_date']);

                break;
            case 'begin':
            case 'activation_date':
                $this->merge(['date' => 'activation_date']);

                break;
            default:
                // default code
                break;
        }
    }

    public function messages(): array
    {
        return [
            'date_format' => 'date format incorrect, the correct format is: Y-m-d (2023-03-15)',
            'date.in' => 'The value for date must be in: created, renewal, begin, activation_date'
        ];

    }
}
