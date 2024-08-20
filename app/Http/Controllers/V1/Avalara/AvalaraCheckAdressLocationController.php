<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\DataObject\PcodeLookOutDO;
use Crater\Http\Controllers\Controller;
use Crater\Models\Address;
use Crater\Models\AvalaraConfig;

class AvalaraCheckAdressLocationController extends Controller
{
    public function show(AvalaraConfig $config, $address_id)
    {
        $address = Address::find($address_id);

        if (is_null($address)) {
            return [
                'success' => false,
                'message' => 'Address id not valid',
            ];
        }

        $service = new AvalaraService(new AvalaraApi($config));

        return $service->getPCode((new PcodeLookOutDO($address->toLocationRequest()))->toString());
    }
}
