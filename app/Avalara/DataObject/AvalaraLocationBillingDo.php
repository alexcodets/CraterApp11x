<?php

namespace Crater\Avalara\DataObject;

use Crater\Models\AvalaraLocation;

class AvalaraLocationBillingDo extends AvalaraBillingDO
{
    public $location;

    public function __construct(AvalaraLocation $model)
    {
        $this->location = $model->location;
    }

    public function getBillingData(): array
    {
        return $this->location;
    }
}
