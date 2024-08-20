<?php

namespace Crater\Avalara\DataObject;

abstract class AvalaraCompanyDO
{
    public int $business_class;

    public int $service_class;

    public int $franchise;

    public bool $facilities;

    public bool $regulated;

    public string $identifier;

    public string $accountReference;

    public function getCompanyData(): array
    {
        return array_filter([
            'bscl' => $this->business_class,
            'svcl' => $this->service_class,
            'fclt' => $this->facilities,
            'frch' => $this->franchise,
            'reg' => $this->regulated,
            'idnt' => $this->identifier,
        ], function ($v) {
            return ! is_null($v);
        });
    }
}
