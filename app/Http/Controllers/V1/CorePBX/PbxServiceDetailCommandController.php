<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\PbxServices;
use Illuminate\Http\Request;

class PbxServiceDetailCommandController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, $pbxService)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServiceDetailCommandController", "Invoke");
        $response = [];
        if (($pbxService = PbxServices::find($pbxService)) == null) {
            $response = [
                'success' => false,
                'message' => 'PbxServices Not Found',
                'data' => [],
            ];
            LogsDev::finishLog($log, $response, $time, 'D', "End show service");

            return response()->json($response);
        }

        $cdr = $pbxService->getPbxCdrs();

        $response = [
            'success' => true,
            'response' => [
                'data' => [
                    'cdr' => [
                        'calculated' => $cdr->calculated()->count(),
                        'unCalculated' => $cdr->unCalculated()->count(),
                        'calculated_today' => $cdr->calculated()->where('billed_at', now()->toDateString())->count(),
                    ],
                    'jobs' => [
                        'import' => $pbxService->is_importing,
                        'calculate' => $pbxService->is_calculating,
                    ],

                ],
            ],
        ];
        LogsDev::finishLog($log, $response, $time, 'D', "End show service");

        return response()->json($response);
    }
}
