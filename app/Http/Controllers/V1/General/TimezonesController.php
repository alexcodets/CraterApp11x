<?php

namespace Crater\Http\Controllers\V1\General;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Space\TimeZones;
use DateTimeZone;
use Illuminate\Http\Request;

class TimezonesController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "TimezonesController", "__invoke");
        //////////////////

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'time_zones' => TimeZones::get_list(),
        ], "message" => "Invoke currencies"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoke currencies");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'time_zones' => TimeZones::get_list(),
        ]);
    }

    public function getContinents(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "listContinent");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $continents = ['Africa', 'America', 'Antarctica', 'Arctic', 'Asia', 'Atlantic', 'Australia', 'Europe', 'Indian', 'Pacific'];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'contienents' => $continents,
        ], "message" => "PbxServersController listContinent"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController listContinent");
        /////////////////////////////////////////

        return response()->json([
            'contienents' => $continents,
            'success' => true,
        ]);
    }

    public function getZoneByContinent(Request $request, $continent)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PbxServersController", "listTimezone");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $continentes = [
            'Africa' => DateTimeZone::AFRICA,
            'America' => DateTimeZone::AMERICA,
            'Antarctica' => DateTimeZone::ANTARCTICA,
            'Arctic' => DateTimeZone::ARCTIC,
            'Asia' => DateTimeZone::ASIA,
            'Atlantic' => DateTimeZone::ATLANTIC,
            'Australia' => DateTimeZone::AUSTRALIA,
            'Europe' => DateTimeZone::EUROPE,
            'Indian' => DateTimeZone::INDIAN,
            'Pacific' => DateTimeZone::PACIFIC,
        ];

        $timezones = DateTimeZone::listIdentifiers($continentes[$continent]);
        // mostrar el segudo elemento despues de / como label y value Africa/Asmara
        $timezones = array_map(function ($value) {
            $valueArr = explode('/', $value);
            // verificar si tiene un tercer elemento
            if (count($valueArr) > 2) {
                $label = $valueArr[1].' - '.$valueArr[2];
            } else {
                $label = $valueArr[1];
            }

            return [
                'label' => $label,
                'value' => $value,
            ];
        }, $timezones);

        // //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'continent' => $continent,
        ], "message" => "PbxServersController listContinent"];
        LogsDev::finishLog($log, $res, $time, 'D', "PbxServersController listContinent");
        /////////////////////////////////////////

        return response()->json([
            'timezones' => $timezones,
            'success' => true,
        ]);
    }

    public function getFullTimezone(Request $request)
    {
        $timezones = DateTimeZone::listIdentifiers();

        return response()->json([
            'timezones' => $timezones,
            'success' => true,
        ]);

    }
}
