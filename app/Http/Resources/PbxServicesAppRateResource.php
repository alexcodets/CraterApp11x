<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\PbxServicesAppRate */

class PbxServicesAppRateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request): array
    {
        return [
            $this->app_name => [
                'id' => $this->id,
                'app_name' => $this->app_name,
                'quantity' => $this->quantity,
                'costo' => $this->costo,
            ]
        ];
    }
}
