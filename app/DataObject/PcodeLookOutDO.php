<?php

namespace Crater\DataObject;

class PcodeLookOutDO
{
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function toString()
    {
        return array_filter([
            "CountryISO" => $this->values['country'] ?? null,
            "State" => $this->values['state'] ?? null,
            "County" => $this->values['county'] ?? null,
            "City" => $this->values['city'] ?? null,
            "ZipCode" => $this->values['zip'] ?? null,
            "BestMatch" => ($this->values['best_match'] ?? null) ? true : false,
            "LimitResults" => $this->values['limit'] ?? null,
            "NpaNxx" => $this->values['npa'] ?? null,
            "Fips" => $this->values['fips'] ?? null,
        ], 'strlen');
    }
}
