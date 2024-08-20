<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\Currency;
use Crater\Models\LogsDev;
use Illuminate\Http\Request;

class CurrenciesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CurrenciesController", "__invoke");
        //////////////////

        $currencies = Currency::latest()->get();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'currencies' => $currencies,
        ], "message" => "Invoke currencies"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoke currencies");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'currencies' => $currencies,
        ]);
    }
}
