<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PbxCdrTenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'tenantid' => $this->tenantid,
            'status' => $this->status ? 'Enable' : 'Disable',
            'enable' => $this->status,
            'date_begin' => $this->date_begin ? $this->date_begin->format('Y-m-d') : '----',
            'last_update' => $this->last_update ? $this->last_update->toDateTimeString() : '----',
            'job_active_at' => $this->job_active_at ? $this->job_active_at->toDateTimeString() : '----',
            'links' => [
                'emable' => route('cdrTenant.enable', ['id' => $this->id]),
                'disable' => route('cdrTenant.disable', ['id' => $this->id]),
                'clean' => route('cdrTenant.cleanJobs', ['id' => $this->id]),
                'reactive-jobs' => route('cdrTenant.enableJobs', ['id' => $this->id]),
            ],
        ];
    }
}
