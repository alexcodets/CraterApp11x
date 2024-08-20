<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\PbxServiceDetailCdrIndexRequest;
use Crater\Models\LogsDev;
use Crater\Models\PbxServices;
use Crater\Pbxware\Service\PbxCdrService;
use Illuminate\Http\JsonResponse;
use Throwable;

class PbxServiceDetailCdrController extends Controller
{
    public function index(PbxServiceDetailCdrIndexRequest $request, PbxServices $pbxService, PbxCdrService $service): JsonResponse
    {
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailCdrController", "index");
        //return 'hola';
        //return array_map('ucfirst', explode(',',request()->status));

        try {
            $cdrs = $service->get($pbxService, []);
            $res = [
                "success" => true,
                "message" => "List service detail success",
                'response' => ['cdr' => $cdrs['items'], 'total' => $cdrs['totals']],

            ];
        } catch (Throwable $th) {
            $res = [
                "success" => false,
                "data" => ['service_cdrs' => [], 'totals' => [], 'cdr' => []],
                "message" => $th->getMessage(),
            ];
        }

        LogsDev::finishLog($log, $res, microtime(true), 'D', "PbxServiceDetailController serviceDetailCdrs");

        return response()->json($res);
    }
}
