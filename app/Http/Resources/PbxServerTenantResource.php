<?php

namespace Crater\Http\Resources;

use Crater\Models\PbxServerTenant;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\PbxServerTenant */
class PbxServerTenantResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'tenant_code' => $this->tenant_code,
            'tenant_id' => $this->tenant_id,
            'status' => $this->status_name,
            'pbx_server_id' => $this->pbx_server_id,
            'creator_id' => $this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'metadata' => [
                'in_use' => $this->is_being_used,
                'server_name' => $this->pbxServer->server_label ?? 'N/A',
                'server_status' => $this->pbxServer->status ?? 'Server not found',
                $this->when($this->status === PbxServerTenant::STATUS_ACTIVE, [
                    'completed_by' => $this->completed_by,
                    'completed_at' => $this->completed_at,
                ]),
            ],

        ];
    }
}
