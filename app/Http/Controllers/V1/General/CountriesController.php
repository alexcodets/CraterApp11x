<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\Country;
use Crater\Models\LogsDev;
//// Models
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CountriesController", "__invoke");
        ///////////////////////////////////////
        ///
        $response = [
            'countries' => Country::all(),
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $response, "message" => "Lista de paises"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Lista de paises");
        /////////////////////////////////////////

        return response()->json($response);
    }
}
