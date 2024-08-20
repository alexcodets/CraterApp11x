<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\SendEstimatesRequest;
use Crater\Models\Estimate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class SendEstimateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Crater\Http\Requests\SendEstimatesRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendEstimatesRequest $request, Estimate $estimate)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Estimate' => $estimate]);
        $log = LogsDev::initLog($request2, "", "D", "SendEstimateController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // Logs por modulo
        LogsModule::createLog("Estimates", "Send", "admin/estimates", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate: ".$estimate->estimate_number);


        ////Log::debug($estimate);

        $response = $estimate->send($request->all());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $response, "message" => "__invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "__invoke");
        /////////////////////////////////////////

        return response()->json($response);
    }
}
