<?php

namespace Crater\Avalara\DataObject;

use Crater\Models\AvalaraExemption;

class AvalaraExemptionDO
{
    public function toArray(AvalaraExemption $ex): array
    {
        return array_filter([
            'frc' => (bool)$ex->frc,
            'loc' => $ex->location->location,
            'tpe' => $ex->tpe ?? 0,
            'dom' => $ex->dom ?? 0,
            'cat' => $ex->cat ?? 0,
            'scp' => $ex->scp ?? null,
            'exnb' => (bool)$ex->exnb,
        ], function ($v) {return ! is_null($v);});
    }
}
