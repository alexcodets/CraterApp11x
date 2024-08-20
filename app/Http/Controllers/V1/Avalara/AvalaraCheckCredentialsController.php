<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraConfig;
use Illuminate\Http\Request;

class AvalaraCheckCredentialsController extends Controller
{
    public function __invoke(AvalaraConfig $config, Request $request)
    {
        //return $config;
        $service = new AvalaraService(new AvalaraApi($config));
        $response = $service->serverStatus();

        return response()->json($response, $response['status']);
    }
}
