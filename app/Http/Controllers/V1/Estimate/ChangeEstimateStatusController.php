<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Estimate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class ChangeEstimateStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Estimate $estimate
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, Estimate $estimate)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Estimate' => $estimate]);
        $log = LogsDev::initLog($request2, "", "D", "ChangeEstimateStatusController", "__invoke");
        ///////////////////////////////////////


        // Logs por modulo
        LogsModule::createLog("Estimates", "Update", "admin/estimates", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate Change Status: ".$estimate->estimate_number);


        $estimate->update($request->only('status'));

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "__invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "__invoke");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }
}
