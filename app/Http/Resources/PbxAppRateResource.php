<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\PbxServicesAppRate */
class PbxAppRateResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            [
                'id' => $this->id,
                'app_name' => $this->app_name,
                'quantity' => $this->quantity,
                'costo' => $this->costo,
                'pbxService' => new PbxServicesResource($this->whenLoaded('pbxService')),
            ],
        ];
    }
}
