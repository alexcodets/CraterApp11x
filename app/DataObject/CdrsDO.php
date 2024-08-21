<?php

namespace Crater\DataObject;

class CdrsDO
{
    public array $cdrs;

    private CdrDO $cdrDo;

    public function __construct(array $cdrs)
    {
        $this->cdrDo = new CdrDO();
        $this->rebuildCdrs($cdrs);
    }

    public function rebuildCdrs(array $cdrs): array
    {
        $this->cdrs = [];
        foreach ($cdrs as $key => $cdr) {
            $this->cdrs[] = $this->cdrDo->getCdr($cdr);
        }

        return $this->cdrs;
    }
}
