<?php

namespace Crater\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketDepartament extends FormRequest
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
        /* $data = [
            'name' => [
                'required',
                'string',
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'stiky' => [
                'nullable',
                'integer',
            ],
            'user_id' => [
                'nullable',
                'integer',
            ],
        ];
        return $data; */
    }
}
