<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\StripeSetting */
class StripeSettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
            'public_key' => $this->public_key,
            'secret_key' => $this->secret_key,
            'status' => $this->status,
            'currency' => $this->currency,
            'environment' => $this->environment,

            'creator_id' => $this->creator_id,
        ];
    }
}
