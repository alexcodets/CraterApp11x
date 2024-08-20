<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Models\AddOns;
use Crater\Models\LogsDev;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getModules(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ModulesController", "getModules");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;
        // consulta bd
        $modules = DB::table('modules')->where("status", "A")->get();

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'modules' => $modules,
        ], "message" => "Listado de modulos"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado de modulos");

        return response()->json([
            'modules' => $modules
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAddOns(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ModulesController", "getAddOns");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;
        // consulta bd
        $addOns = DB::table('add_ons')->get();

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'addOns' => $addOns,
        ], "message" => "Listado de modulos add ons"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado de modulos add ons");

        return response()->json([
            'addOns' => $addOns
        ]);

    }

    /**
     * update status field of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAddOnStatus(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ModulesController", "updateAddOnsStatus");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        /* //Log::debug('controller--');
        //Log::debug($id); */

        // consulta bd
        $addOns = AddOns::find($id);
        $addOns->status = $addOns->status == 'A' ? 'I' : 'A';
        $updated = $addOns->save();

        if ($updated) {
            $response = [
                'status' => 200,
                'success' => true,
            ];
        } else {
            $response = [
                'status' => 406,
                'success' => false,
            ];
        }


        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'addOns' => $addOns,
        ], "message" => "Update de add ons status"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Update de add ons status");

        return response()->json([
            'addOns' => $addOns,
            'success' => $response['success']
        ]);

    }
}
