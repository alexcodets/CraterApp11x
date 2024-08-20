<?php

namespace Crater\Http\Controllers\V1\CorePBX;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
// models
use Crater\Models\LogsModule;
use Crater\Models\PbxJobLog;
use Illuminate\Http\Request;

class PbxHostedManage extends Controller
{
    /**
     *
     */
    public function getPbxJobsLogs(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxHostedManage", "getPbxJobsLogs");


        $orderBy = $request->has('orderBy') ? $request["orderBy"] : 'desc';
        $orderByField = $request->has('orderByField') ? $request["orderByField"] : 'id';
        // listar pbx job log con paginado
        $data = PbxJobLog::select('*')
        ->with('pbxService:id,pbx_services_number,customer_id,deleted_at', 'customer:id,customcode,deleted_at')
        ->orderBy('id', 'DESC')
        ->wherePbxService($request->pbx_services_number)
        ->whereDate($request)
        ->where("response", "!=", "Crater\Jobs\PbxImportCdrJob has been attempted too many times or run too long. The job may have previously timed out.")
        // ->whereCustomer($request->customer)
        ->orderBy($orderByField, $orderBy)
        ->paginate(10);

        $res = ["success" => true, "response" => ["datamesage" => [
            'data' => $data,
        ], "message" => "PBX Config getPbxJobsLogs"]];

        LogsDev::finishLog($log, $res, $time, 'D', "PBX getPbxJobsLogs");

        LogsModule::createLog(
            "PBX job log",
            "List logs",
            "admin/pbx/jobs/logs",
            0,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id
        );

        return response()->json([
            'success' => true,
            "message" => "PBX getPbxJobsLogs",
            'data' => $data
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
