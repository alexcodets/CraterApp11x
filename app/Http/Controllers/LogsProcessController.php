<?php

namespace Crater\Http\Controllers;

use Crater\Models\LogsDev;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Log;

class LogsProcessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogsDev(Request $request)
    {

        //Log::debug(Auth::user());  Se obtiene el usuario en curso
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "LogsProcessController", "getLogsDev");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;
        $logsdev = LogsDev::select("id", "ip", "controller", "method", "request", "message")->PaginateData($limit);

        $res = ["success" => true, "response" => ["datamesage" => [
            'logsDev' => $logsdev,
            'logsDevTotalCount' => LogsDev::get()->count(),
        ], "message" => "Listado logsDevs"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado logsDevs");

        return response()->json([
            'logsDev' => $logsdev,
            'logsDevTotalCount' => LogsDev::get()->count(),
        ]);


    }
}
