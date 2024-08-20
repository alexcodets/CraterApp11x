<?php

namespace Crater\Http\Requests;

class PbxServerTenantShowRequest extends GenericIndexRequest
{
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|min:3|max:255',
            'code' => 'nullable|int|between:200,999',
            'server_name' => 'nullable|string|max:255',
            'limit' => 'bail|nullable|integer|min:2|max:100',
        ];
    }
}
