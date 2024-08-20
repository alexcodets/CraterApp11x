<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Models\EmailLog;
use Crater\Models\LogsDev;
use Illuminate\Http\Request;

class EmailLogsController extends Controller
{
    public function index(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "EmailLogsController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $emailLogs = EmailLog::whereCompany($request->header('company'))
            ->applyFilters(
                $request->only([
                    'email',
                    'subject',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy'
                ])
            )
            ->paginateData($limit);

        $count = EmailLog::count();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'emailLogs' => $emailLogs,
                    'emailLogsTotalCount' => $count,
                ],
                "message" => "Listado de Logs por Email"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Fin de index Logs por Email");

        return response()->json([
            'emailLogs' => $emailLogs,
            'emailLogsTotalCount' => $count
        ]);
    }
}
