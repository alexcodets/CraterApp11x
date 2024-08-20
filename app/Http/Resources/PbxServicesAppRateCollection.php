<?php

namespace Crater\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PbxServicesAppRateCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => $this->collection,
            'pagination' => [
                "current_page" => $this->currentPage(),
                "first_page_url" => $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'=1',
                "prev_page_url" => $this->previousPageUrl(),
                "next_page_url" => $this->nextPageUrl(),
                "last_page_url" => $this->getOptions()['path'].'?'.$this->getOptions()['pageName'].'='.$this->lastPage(),
                "last_page" => $this->lastPage(),
                "per_page" => $this->perPage(),
                "total" => $this->total(),
                "path" => $this->getOptions()['path'],
            ],
        ];
    }
}
