<?php

namespace Crater\Http\Requests;

class AvalaraItemsTaxRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'items' => 'array',
            'items.*.id' => 'required|int',
            'items.*.quantity' => 'required|int',
            'items.*.total' => 'required|int',
            'items.*.price' => 'required|int'
        ];
    }
}
