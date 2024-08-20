<?php

namespace Crater\Avalara\DataObject;

class AvalaraCompanyDataDO extends AvalaraCompanyDO
{
    public function __construct(int $businessClass, int $service_class, bool $facilities, bool $regulated, bool $franchise)
    {
        $this->business_class = $businessClass;
        $this->service_class = $service_class;
        $this->facilities = $facilities;
        $this->regulated = $regulated;
        $this->franchise = $franchise;
    }
}
