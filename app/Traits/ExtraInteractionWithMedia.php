<?php

namespace Crater\Traits;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait ExtraInteractionWithMedia
{
    public function getExpenseDocById(string $collectionName, $id): ?Media
    {
        $media = $this->getMedia($collectionName)->where("id", $id);

        return $media->first();
    }
}
