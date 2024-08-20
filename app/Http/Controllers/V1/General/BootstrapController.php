<?php

namespace Crater\Http\Controllers\V1\General;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;

//// Models
use Crater\Models\Country;
use Crater\Models\Currency;
use Crater\Models\LogsDev;
use Illuminate\Http\Request;

class BootstrapController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "BootstrapController", "__invoke");
        /////////////////////////////////////////////

        $user = Auth::user();
        $default_language = $user->getSettings(['language'])['language'];

        $settings = [
            'moment_date_format',
            'carbon_date_format',
            'fiscal_year',
            'time_zone',
            'currency',
            'idle_time_logout',
            'header_color',
            'primary_color',
        ];

        $settings = CompanySetting::getSettings($settings, $user->company_id);
        $default_currency = Currency::findOrFail($settings['currency']);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $response = [
            'user' => $user,
            'company' => $user->company,
            'currencies' => Currency::all(),
            'countries' => Country::all(),
            'default_currency' => $default_currency,
            'default_language' => $default_language,
            'moment_date_format' => $settings['moment_date_format'],
            'carbon_date_format' => $settings['carbon_date_format'],
            'fiscal_year' => $settings['fiscal_year'],
            'time_zone' => $settings['time_zone'],
            'idle_time_logout' => $settings['idle_time_logout'] ?? 5,
            'header_color' => $settings['header_color'] ?? null,
            'primary_color' => $settings['primary_color'] ?? null,
        ];
        $res = ["success" => true, "response" => ["datamesage" => $response, "message" => "Bootstrap invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Bootstrap invoke");
        ///////////////////

        return response()->json($response);
    }
}
