<?php

namespace Crater\Avalara\DataObject;

use Exception;

class AvalaraUserBillingDO extends AvalaraBillingDO
{
    /**
     * @throws Exception
     */
    public function __construct($model)
    {
        if (is_null($model)) {
            throw new Exception(__('avalara.error.location.address.required.model'));
        }

        if (! $model->state) {
            throw new Exception(__('avalara.error.location.address.required.state'));
        }

        if (! $model->country) {
            throw new Exception(__('avalara.error.location.address.required.country'));
        }

        $this->state = $model->state->code;
        $this->country = $model->country->code;
        $this->zip = $model->zip;
        $this->city = $model->city;
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
