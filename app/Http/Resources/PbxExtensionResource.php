<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\PbxExtensions */
class PbxExtensionResource extends JsonResource
{
    public function toArray($request): array
    {
        $data = array_filter([
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'name' => $this->name,
            'ext' => $this->ext,
            'extensionid' => $this->extensionid,
            'email' => $this->email,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'api_id' => $this->api_id,
            'linenum' => $this->linenum,
            'location' => $this->location,
            'auto_provisioning' => $this->auto_provisioning,
            'dhcp' => $this->dhcp,
            'static_ip' => $this->static_ip,
            'time_zone' => $this->time_zone,
            'macaddress' => $this->macaddress,
            'protocol' => $this->protocol,
            'pin' => $this->pin,
            'ua_fullname' => $this->ua_fullname,
            'ua_id' => $this->ua_id,
            'ua_name' => $this->ua_name,
            'cost_per_day' => $this->cost_per_day,
            'prorate' => $this->prorate,
            'date_prorate' => $this->date_prorate,
            'pbxext_id' => $this->pbxext_id,
            'pbx_tenant_code' => $this->pbx_tenant_code,
            'deleted_in_server' => $this->deleted_in_server,
            'full_name' => $this->full_name,
            'custom_searches_count' => $this->custom_searches_count,
            'departments_count' => $this->departments_count,
            'disabled_logs_count' => $this->disabled_logs_count,
            'pbx_service_count' => $this->pbx_service_count,
            'schedule_logs_count' => $this->schedule_logs_count,
            'schedule_status_logs_count' => $this->schedule_status_logs_count,
            //'pbxService' => PbxServicesResource::collection($this->whenLoaded('pbxService')),

        ], 'strlen');

        $data['service'] = new PbxServicesResource($this->whenLoaded('pbxSingleService'));


        return $data;
    }
}
