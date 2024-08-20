<?php

namespace Crater\Http\Requests;

class CustomerTicketRequest extends BaseApiRequest
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
            'summary' => [
                'required',
                'string',
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'priority' => [
                'nullable',
                'string',
            ],
            'status' => [
                'nullable',
                'string',
            ],
            'dep_id' => [
                'nullable',
                'integer',
            ],
            'assigned_id' => [
                'nullable',
                'integer',
            ],
            'user_id' => [
                'nullable',
                'integer',
            ],
            'company_id' => [
                'nullable',
                'integer'
            ],
            'ticket_number' => [
                'nullable',
                'string'
            ],
            'date' => 'nullable',
            'time' => 'nullable',
        ];
    }
}
