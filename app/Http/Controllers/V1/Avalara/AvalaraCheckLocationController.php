<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Crater\Avalara\Apis\AvalaraApi;
use Crater\Avalara\Service\AvalaraService;
use Crater\DataObject\PcodeLookOutDO;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PcodeLookupRequest;
use Crater\Models\AvalaraConfig;

class AvalaraCheckLocationController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(AvalaraConfig $config, PcodeLookupRequest $request)
    {
        $service = new AvalaraService(new AvalaraApi($config));
        $data = $request->validated();
        if ($data == []) {
            return [
                "success" => false,
                "message" => "At least one field must contain a value",
                "data" => [],
            ];
        }

        return $service->getPCode((new PcodeLookOutDO($data))->toString());
    }
}
