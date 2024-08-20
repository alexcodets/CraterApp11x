<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraLog;
use Crater\Models\PbxServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PbxServiceDetailAvalaraController extends Controller
{
    public function __invoke(Request $request, PbxServices $pbxService): JsonResponse
    {
        $avaLog = $pbxService->avalaraLogs();

        $totals = $pbxService->avalaraLogs()->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when type = ? then 1 end) as invoice', [AvalaraLog::INVOICE_MANUAL])
            ->selectRaw('count(case when type = ? then 1 end) as service', [AvalaraLog::INVOICE_SERVICE])
            ->selectRaw('count(case when status = ? then 1 end) as error', [AvalaraLog::STATUS_ERROR])
            ->selectRaw('count(case when status = ? then 1 end) as success', [AvalaraLog::STATUS_SUCCESS])->first();

        $month = $pbxService->avalaraLogs()->thisMonth()->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when type = ? then 1 end) as invoice', [AvalaraLog::INVOICE_MANUAL])
            ->selectRaw('count(case when type = ? then 1 end) as service', [AvalaraLog::INVOICE_SERVICE])
            ->selectRaw('count(case when status = ? then 1 end) as error', [AvalaraLog::STATUS_ERROR])
            ->selectRaw('count(case when status = ? then 1 end) as success', [AvalaraLog::STATUS_SUCCESS])->first();

        $today = $pbxService->avalaraLogs()->today()->toBase()
            ->selectRaw('count(*) as total')
            ->selectRaw('count(case when type = ? then 1 end) as invoice', [AvalaraLog::INVOICE_MANUAL])
            ->selectRaw('count(case when type = ? then 1 end) as service', [AvalaraLog::INVOICE_SERVICE])
            ->selectRaw('count(case when status = ? then 1 end) as error', [AvalaraLog::STATUS_ERROR])
            ->selectRaw('count(case when status = ? then 1 end) as success', [AvalaraLog::STATUS_SUCCESS])->first();

        $response = [
            'success' => true,
            'response' => [
                'data' => [
                    'taxes' => [
                        'totals' => [
                            $totals,
                        ],
                        'today' => [
                            $today,
                        ],
                        'month' => [
                            $month,
                        ],
                    ],
                ],
            ],
        ];

        return response()->json($response);
    }
}
