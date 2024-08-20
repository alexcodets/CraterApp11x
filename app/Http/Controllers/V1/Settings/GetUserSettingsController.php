<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\GetSettingsRequest;
use Crater\Models\LogsDev;

class GetUserSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\GetSettingsRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(GetSettingsRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "GetUserSettingsController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $user = Auth::user();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => $user->getSettings($request->settings) , "message" => "GetUserSettingsController"];
        LogsDev::finishLog($log, $res, $time, 'D', "GetUserSettingsController");
        /////////////////////////////////////////

        return response()->json($user->getSettings($request->settings));
    }
}
