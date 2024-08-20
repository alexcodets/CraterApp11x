<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \Crater\Models\TransactionFees */
class TransactionFeesResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'id' => $this->id,
        ];
    }
}
