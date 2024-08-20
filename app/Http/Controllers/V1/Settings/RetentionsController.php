<?php

namespace Crater\Http\Controllers\V1\Settings;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\RetentionsRequest;
// models
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Retentions;
use Illuminate\Http\Request;
use Log;

class RetentionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RetentionsController", "index");

        $limit = $request->limit ?? 10;

        $resRetentions = Retentions::select('*')
                        ->applyFilters($request->only(['concept']))
                        ->paginate($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServers' => $resRetentions,
        ], "message" => "RetentionsController get"];
        LogsDev::finishLog($log, $res, $time, 'D', "RetentionsController index");
        /////////////////////////////////////////

        return response()->json([
            'retentions' => $resRetentions,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Crater\Http\Requests\RetentionsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RetentionsRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RetentionsController", "store");

        $data = $request->validated();
        $data['foreing'] = $data['foreing'] === 'yes' ? 1 : 0;
        $resRetention = Retentions::create($data);

        if ($resRetention) {
            $response = [
                'status' => 200,
                'response' => 'Saved correctly',
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving',
            ];
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'pbxServers' => $resRetention,
        ], "message" => "RetentionsController post"];
        LogsDev::finishLog($log, $res, $time, 'D', "RetentionsController store");
        /////////////////////////////////////////

        return response()->json([
            'retention' => $resRetention,
            'success' => true,
            'status' => $response['status']
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request  $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RetentionsController", "show");

        $retention = Retentions::find($id);

        if ($retention) {
            $response = [
                'status' => 200,
                'message' => 'Retention found it',
                'success' => true,
                'retention' => $retention
            ];
        } else {
            $response = [
                'status' => 406,
                'message' => 'Error founding Retention',
                'success' => false,

            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "Retention show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Retentions", "show", "admin/retention/{id}", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Crater\Http\Requests\RetentionsRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RetentionsRequest $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RetentionsController", "update");

        if($request->has('id')) {
            $data = $request->validated();
            $retention = Retentions::find($request->input('id'));

            $retention->concept = $data['concept'];
            $retention->minimium_base_per_unit = $data['minimium_base_per_unit'];
            $retention->minimium_base_in_currency = $data['minimium_base_in_currency'];
            $retention->type_of_minimium_base_in_currency = $data['type_of_minimium_base_in_currency'];
            $retention->percentage = $data['percentage'];
            $retention->foreing = $data['foreing'];
            $retention->country_id = $data['country_id'];
            $retention->great_contributor = $data['great_contributor'];
            $retention->self_retaining = $data['self_retaining'];
            $retention->simple_tax_regime = $data['simple_tax_regime'];
            $retention->vat_withholding_agent = $data['vat_withholding_agent'];


            $updated = $retention->save();

            if ($updated) {
                $response = [
                    'status' => 200,
                    'response' => 'Retention update correctly',
                ];
            } else {
                $response = [
                    'status' => 406,
                    'response' => 'Error updating Retention',
                ];
            }

        }


        LogsDev::finishLog($log, $response, $time, 'D', "Retention update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Retention", "Update", "admin/customer/address", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "RetentionsController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $retention = Retentions::find($id);
        $deleted = $retention->delete();

        if ($deleted) {
            $response = [
                'status' => 200,
                'response' => 'Retention deleted correctly',
                'success' => true
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error deleting Retention',
                'success' => false
            ];
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "soft delete success"];
        LogsDev::finishLog($log, $res, $time, 'D', "soft delete success");
        /////////////////////////////////////////

        return response()->json($response, $response['status']);
    }
}
