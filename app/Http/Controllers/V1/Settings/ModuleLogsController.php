<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class ModuleLogsController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "ModuleLogsController", "index");

        $limit = $request->has('limit') ? $request->limit : 10;

        $moduleLogs = LogsModule::whereCompany($request->header('company'))
            ->applyFilters(
                $request->only([
                    'module',
                    'task',
                    'username',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy'
                ])
            )
            ->paginateData($limit);

        $count = LogsModule::count();

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'moduleLogs' => $moduleLogs,
                    'moduleLogsTotalCount' => $count,
                ],
                "message" => "Listado de Logs por Modulo"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Fin de index Logs por Modulo");

        return response()->json([
            'moduleLogs' => $moduleLogs,
            'moduleLogsTotalCount' => $count
        ]);
    }

    public function getSearchLists(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "ModuleLogsController", "getSearchLists");

        $limit = $request->has('limit') ? $request->limit : 'all';

        $listModules = LogsModule::select('module')
            ->distinct()
            ->orderBy('module')
            ->paginateData($limit);

        $listTasks = LogsModule::select('task')
            ->distinct()
            ->orderBy('task')
            ->paginateData($limit);

        $res = [
            "success" => true,
            "response" => [
                "datamesage" => [
                    'listModules' => $listModules,
                    'listTasks' => $listTasks
                ],
                "message" => "Listado de Nombres de Modulos y tareas"
            ]
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Fin Listado de Nombres de Modulos y tareas");

        return response()->json([
            'listModules' => $listModules,
            'listTasks' => $listTasks
        ]);
    }
}
