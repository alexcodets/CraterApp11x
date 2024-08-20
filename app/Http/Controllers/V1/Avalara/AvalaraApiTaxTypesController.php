<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraConfig;

class AvalaraApiTaxTypesController extends Controller
{
    public function index(AvalaraConfig $config)
    {
        $ser = new AvalaraService(new AvalaraApi($config));

        return $ser->getTaxTypes();
    }
}
