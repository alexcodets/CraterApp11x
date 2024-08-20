<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\PbxDID */
class PbxDidResource extends JsonResource
{
    public function toArray($request): array
    {
        $values = array_filter([
            'id' => $this->id,
            'company_id' => $this->company_id,
            'creator_id' => $this->creator_id,
            'didid' => $this->didid,
            'server' => $this->server,
            'status' => $this->status,
            'pbx_tenant_id' => $this->pbx_tenant_id,
            'trunk' => $this->trunk,
            'type' => $this->type,
            'type_name' => $this->type_name,
            'number' => $this->number,
            'number2' => $this->number2,
            'ext' => $this->ext,
            'e164_2' => $this->e164_2,
            'e164' => $this->e164,
            'api_id' => $this->api_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'cost_per_day' => $this->cost_per_day,
            'prorate' => $this->prorate,
            'date_prorate' => $this->date_prorate,
            'pbxdid_id' => $this->pbxdid_id,
            'pbx_tenant_code' => $this->pbx_tenant_code,
            'pbx_server_id' => $this->pbx_server_id,
            'deleted_in_server' => $this->deleted_in_server,
        ], 'strlen');

        $values['pbxService'] = $this->when(
            $this->relationLoaded('pbxServiceDid') ?? null &&
            $this->pbxServiceDid->relationLoaded('service') ?? null,
            function () {
                return new PbxServicesResource($this->pbxServiceDid->service ?? null) ;
            }
        );

        return $values;
    }
}
