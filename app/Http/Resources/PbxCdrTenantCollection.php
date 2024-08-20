<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PbxCdrTenantCollection extends ResourceCollection
{
    public $collects = PbxCdrTenantResource::class;

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        //return parent::toArray($request);
        return [
            'data' => $this->collection,
            'links' => [
                'main' => route('cdrTenant.index'),
            ],
        ];
    }
}
