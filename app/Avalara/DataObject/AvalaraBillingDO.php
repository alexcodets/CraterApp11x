<?php

namespace Crater\Avalara\DataObject;

abstract class AvalaraBillingDO
{
    public string $country;

    public string $state;

    public string $city;

    public int $zip;

    abstract public function getBillingData(): array;
}
