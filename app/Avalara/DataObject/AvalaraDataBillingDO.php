<?php

namespace Crater\Avalara\DataObject;

class AvalaraDataBillingDO extends AvalaraBillingDO
{
    public function __construct(string $country,  string $state, string $city, int $zip)
    {
        $this->country = $country;
        $this->zip = $zip;
        $this->state = $state;
        $this->city = $city;
    }

    public function getBillingData(): array
    {
        return [
            'ctry' => $this->country,
            'zip' => $this->zip,
            'st' => $this->state,
            'city' => $this->city,
        ];

    }
}
