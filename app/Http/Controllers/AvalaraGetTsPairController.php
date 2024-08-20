<?php

namespace Crater\Http\Controllers;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Models\AvalaraConfig;

class AvalaraGetTsPairController extends Controller
{
    public function __invoke(AvalaraConfig $config): array
    {
        $service = new AvalaraService(new AvalaraApi($config));

        return $service->getTSPair();
    }
}
