<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\UpdateSettingsRequest;
use Crater\Models\LogsDev;

class UpdateUserSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\UpdateSettingsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(UpdateSettingsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "UpdateUserSettingsController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo



        $user = Auth::user();

        $user->setSettings($request->settings);


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
        'success' => true
        ] , "message" => "UpdateUserSettingsController"];
        LogsDev::finishLog($log, $res, $time, 'D', "UpdateUserSettingsController");
        /////////////////////////////////////////


        return response()->json([
            'success' => true
        ]);
    }
}
