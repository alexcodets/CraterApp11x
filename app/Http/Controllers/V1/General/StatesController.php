<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\State;
use Illuminate\Http\Request;

class StatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, $code)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "StatesController", "__invoke");
        ///////////////////////////////////////

        $states = State::select('*')->where('country_alpha2', $code)->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'states' => $states,
        ], "message" => "States list"]];
        LogsDev::finishLog($log, $res, $time, 'D', "States list");
        /////////////////////////////////////////

        return response()->json([
            'states' => $states,
        ]);
    }
}
