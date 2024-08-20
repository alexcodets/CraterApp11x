<?php

namespace Crater\Http\Requests;

class PbxServerTenantRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'tenant_code' => ['required'],
            'tenant_id' => ['required'],
            'status' => ['required', 'integer'],
            'pbx_server_id' => ['required', 'integer'],
            'creator_id' => ['required', 'integer'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
