<?php

namespace Crater\Http\Requests;

class PbxServiceDetailCdrIndexRequest extends BaseApiRequest
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
            'limit' => 'sometimes|integer',
            'custom' => 'sometimes|bool',
            'only_custom' => 'sometimes|bool',
            'page' => 'sometimes|integer',
            'order_by' => 'sometimes|string',
            'order' => 'sometimes|in:asc,desc',
            'start_date' => ['sometimes', 'regex:/(\d{4}-\d{2}-\d{2}_\d{2}:\d{2}:\d{2})/u'],
            'end_date' => ['sometimes', 'regex:/(\d{4}-\d{2}-\d{2}_\d{2}:\d{2}:\d{2})/u'],
            'csv' => 'sometimes|bool',
            'from' => 'sometimes',
            'to' => 'sometimes',
            'id' => 'sometimes',
            'status' => 'sometimes',
            'cdr_type' => 'sometimes|bool',
            'cdrType' => 'sometimes|int|between:0,4',
            'paid' => 'sometimes|in:pending,billed,all',
            'type_custom' => 'sometimes|int|between:0,4'
        ];
    }
}
