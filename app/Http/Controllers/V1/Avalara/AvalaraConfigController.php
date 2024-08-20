<?php

namespace Crater\Http\Controllers\V1\Avalara;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\AvalaraConfig;
use Crater\Models\AvalaraLog;
use Crater\Models\CompanySetting;
use Crater\Models\Item;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AvalaraConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 25;

        $avalara_configs = AvalaraConfig::applyFilters($request->only([
            'conexion',
            'user_name',
            'client_id',
            'url',
            'host',
            'status',
            'orderByField',
            'orderBy'
        ]))
        ->whereCompany($request->header('company'))
        ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'avalara_configs' => $avalara_configs,
        ], "message" => "Avalara Config index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "AvalaraConfig index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Avalara Configs", "List", "admin/avalara/config", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        $response = [
            'status' => 200,
            'list' => $avalara_configs,
            'avalaraConfigsTotalCount' => AvalaraConfig::count()
        ];

        return response()->json($response, $response['status']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $validator = Validator::make($request->all(), [
            'conexion' => 'required',
            'user_name' => 'required',
            'password' => 'required',
            'client_id' => 'required',
            'url' => 'required',
            'host' => 'required'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 406,
                'response' => 'There are some required fields missing.'
            ];
            response()->json($response, $response['status']);
        }

        $newAvalaraConfig = AvalaraConfig::firstWhere('user_name', $request->input('user_name'));

        /* if ($newAvalaraConfig) {
            $response = [
                'status' => 406,
                'response' => 'The User Name is already in use'
            ];
            return response()->json($response, $response['status']);
        } */

        $company_id = Auth::user()->company_id;

        if ($request->input('status') == 'A') {
            AvalaraConfig::where(['conexion' => $request->input('conexion')])->update(['status' => 'I']);
        }

        $newAvalaraConfig = new AvalaraConfig();
        $newAvalaraConfig->conexion = $request->input('conexion');
        $newAvalaraConfig->user_name = $request->input('user_name');
        $newAvalaraConfig->password = $request->input('password');
        $newAvalaraConfig->client_id = $request->input('client_id');
        $newAvalaraConfig->status = $request->input('status');
        $newAvalaraConfig->url = $request->input('url');
        $newAvalaraConfig->host = $request->input('host');
        $newAvalaraConfig->bscl = $request->input('bscl');
        $newAvalaraConfig->svcl = $request->input('svcl');
        $newAvalaraConfig->fclt = $request->input('fclt');
        $newAvalaraConfig->reg = $request->input('reg');
        $newAvalaraConfig->frch = $request->input('frch');
        $newAvalaraConfig->item_did_id = $request->input('item_did_id');
        $newAvalaraConfig->item_cdr_id = $request->input('item_cdr_id');
        $newAvalaraConfig->item_extension_id = $request->input('item_extension_id');
        $newAvalaraConfig->item_custom_id = $request->input('item_custom_id');
        $newAvalaraConfig->item_international_id = $request->input('item_international_id');
        $newAvalaraConfig->item_toll_free_id = $request->input('item_toll_free_id');
        $newAvalaraConfig->custom_app_rate_item_id = $request->input('custom_app_rate_item_id');
        $newAvalaraConfig->services_price_item_id = $request->input('services_price_item_id');
        $newAvalaraConfig->additional_charges_item_id = $request->input('additional_charges_item_id');
        $newAvalaraConfig->profile_id = $request->input('profile_id');
        $newAvalaraConfig->company_identifier = $request->input('company_identifier');
        $newAvalaraConfig->account_reference = $request->input('account_reference');
        $newAvalaraConfig->invm = $request->input('invm');
        $newAvalaraConfig->dtl = $request->input('dtl');
        $newAvalaraConfig->summ = $request->input('summ');
        $newAvalaraConfig->retnb = $request->input('retnb');
        $newAvalaraConfig->retext = $request->input('retext');
        $newAvalaraConfig->incrf = $request->input('incrf');

        $newAvalaraConfig->company_id = $company_id;

        $saved = $newAvalaraConfig->save();

        if ($saved) {
            $response = [
                'status' => 200,
                'response' => 'Avalara Config stored correctly'
            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "Avalara Config store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("AvalaraConfig", "Store", "admin/avalara/config/create", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\avalara_config  $avalara_config
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $requestId = ['id' => $id];
        $validator = Validator::make($requestId, [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 406,
                'response' => 'Id is required.'
            ];

            return response()->json($response, $response['status']);
        }


        $avalaraConfig = AvalaraConfig::find($id);
        $avalaraConfig['password_decode'] = $avalaraConfig['password'];

        if ($avalaraConfig) {
            $response = [
                'status' => 200,
                'response' => $avalaraConfig
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Package not found'
            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "Packages show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Packages", "Show", "admin/avalara/config/id", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\avalara_config  $avalara_config
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AvalaraConfig $avalara_config)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            $response = [
                'status' => 406,
                'response' => 'There are some required fields missing.'
            ];

            return response()->json($response, $response['status']);
        }

        $company_id = Auth::user()->company_id;
        $lang = CompanySetting::getSetting('language', $company_id);

        if ($request->input('status') == 'A') {
            AvalaraConfig::where(['conexion' => $request->input('conexion')])->update(['status' => 'I']);
        }

        $avalara_config = AvalaraConfig::find($request->input('id'));

        $avalara_config->conexion = $request->input('conexion');
        $avalara_config->user_name = $request->input('user_name');
        $avalara_config->password = $avalara_config->setPasswordAttribute($request->input('password'));
        $avalara_config->client_id = $request->input('client_id');
        $avalara_config->status = $request->input('status');
        $avalara_config->profile_id = $request->input('profile_id');
        $avalara_config->company_identifier = $request->input('company_identifier');
        $avalara_config->url = $request->input('url');
        $avalara_config->host = $request->input('host');
        $avalara_config->company_id = $company_id;
        $avalara_config->bscl = $request->input('bscl');
        $avalara_config->svcl = $request->input('svcl');
        $avalara_config->fclt = $request->input('fclt');
        $avalara_config->reg = $request->input('reg');
        $avalara_config->frch = $request->input('frch');
        $avalara_config->item_did_id = $request->input('item_did_id');
        $avalara_config->item_cdr_id = $request->input('item_cdr_id');
        $avalara_config->item_extension_id = $request->input('item_extension_id');
        $avalara_config->item_custom_id = $request->input('item_custom_id');
        $avalara_config->item_international_id = $request->input('item_international_id');
        $avalara_config->item_toll_free_id = $request->input('item_toll_free_id');
        $avalara_config->custom_app_rate_item_id = $request->input('custom_app_rate_item_id');
        $avalara_config->services_price_item_id = $request->input('services_price_item_id');
        $avalara_config->additional_charges_item_id = $request->input('additional_charges_item_id');
        $avalara_config->account_reference = $request->input('account_reference');
        $avalara_config->invm = $request->input('invm');
        $avalara_config->dtl = $request->input('dtl');
        $avalara_config->summ = $request->input('summ');
        $avalara_config->retnb = $request->input('retnb');
        $avalara_config->retext = $request->input('retext');
        $avalara_config->incrf = $request->input('incrf');

        $updated = $avalara_config->save();

        if ($updated) {
            $response = [
                'status' => 200,
                'response' => 'Avalara Config update correctly'
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving Avalara Config'
            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "AvalaraConfig update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("AvalaraConfig", "Update", "admin/avalara/config/id/edit", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\avalara_config  $avalara_config
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $avalara_config = AvalaraConfig::find($id);

        if ($avalara_config->delete()) {
            return response()->json([
                'success' => true,
                'message' => 'Avalara Config destroy correctly'
            ]);
        }
    }

    public function getAvalaraItems(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "getAvalaraItems");

        $items = Item::whereCompany($request->header('company'))
            ->where('avalara_bool', true)
            ->get();

        $res = ["success" => true, "response" => ["datamesage" => [
            'items' => $items,
        ], "message" => "Avalara Config getAvalaraItems"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Avalara Config getAvalaraItems");

        LogsModule::createLog(
            "Avalara config",
            "List Items",
            "admin/avalara/config/avalara_items",
            0,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id
        );

        return response()->json([
            'items' => $items,
            'success' => true
        ]);
    }

    public function getAvalaraLogs(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "getAvalaraLogs");


        //listar AvalaraLog con paginado
        $data = AvalaraLog::with(
            'invoice:id,invoice_number,pbxservice_date_prev,invoice_date,deleted_at',
            'pbxService:id,pbx_services_number,term,deleted_at',
            'customer:id,customcode,deleted_at'
        )->whereCustomer($request->customer)
                                ->whereInvoice($request->invoice_number)
                                ->wherePbxService($request->pbx_services_number)
                                ->whereDates($request)
                                // ordenar por invoice_number en la relacion invoice
                                ->orderColumn($request->orderByField, $request->orderBy)
                                // ->orderBy($request->orderByField, $request->orderBy)
                                ->paginate(10);

        $res = ["success" => true, "response" => ["datamesage" => [
            'data' => $data,
        ], "message" => "Avalara Config getAvalaraLogs"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Avalara Config getAvalaraLogs");

        LogsModule::createLog(
            "Avalara config",
            "List logs",
            "admin/avalara/config/logs",
            0,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id
        );

        return response()->json([
            'success' => true,
            "message" => "Avalara Config getAvalaraLogs",
            'data' => $data
        ]);
    }

    public function getAvalaraDefault(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, '', 'D', 'AvalaraConfigController', 'getAvalaraDefault');

        $data = AvalaraConfig::where('status', 'A')
        ->where('company_id', $request->header('company'))
        ->first();
        if ($data) {
            $data->makeHidden(['password', 'user_name', 'url']);
        }

        $res = [
            'success' => true,
            'response' => ['datamesage' => [
            'data' => $data,
        ], 'message' => 'Avalara Config getAvalaraDefault']
        ];

        LogsDev::finishLog($log, $res, $time, 'D', 'Avalara Config getAvalaraDefault');

        LogsModule::createLog(
            'Avalara config',
            'Avalara default',
            'admin/avalara/default',
            0,
            Auth::user()->name,
            Auth::user()->email,
            Auth::user()->role,
            Auth::user()->company_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Avalara Config getAvalaraDefault',
            'data' => $data
        ]);
    }

    /**
     * update status and defaut the specified resource from storage.
     *
     * @param  \Crater\Models\avalara_config  $avalara_config
     * @return \Illuminate\Http\Response
     */
    public function setAvalaraDefault(Request $request, $id)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AvalaraConfigController", "setAvalaraDefault");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //Log::debug('request');
        //Log::debug($request);

        DB::update('
            UPDATE avalara_configs SET intDefault = ?, status = ? WHERE id != ?
        ', [0, 'I', $id]);

        /* DB::update('
            UPDATE avalara_configs SET intDefault = ?, status = ? WHERE deleted_at = ?
        ', [0, 'I', null]); */

        $avalara_config = AvalaraConfig::find($id);
        $avalara_config->status = 'A';
        $avalara_config->intDefault = 1;
        // $avalara_config->save();

        $updated = $avalara_config->save();

        if ($updated) {
            $response = [
                'status' => 200,
                'response' => 'Avalara Config update correctly',
                'success' => true
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving Avalara Config',
                'success' => false
            ];
        }



        LogsDev::finishLog($log, $response, $time, 'D', "AvalaraConfig setAvalaraDefault");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("AvalaraConfig", "setAvalaraDefault", "admin/avalara/config/default/{id}", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }
}
