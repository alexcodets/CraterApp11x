<?php

namespace Crater\Authorize\Controllers;

use Auth;
use Crater\Authorize\Models\AuthorizeSetting;
use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use DB;
// use Crater\Authorize\Models\Authorize;
use Illuminate\Http\Request;

class AuthorizeSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "index");
        ////////////////

        //// Obtener el registro del usuario logueado
        $user_id = Auth::user()->id;

        $limit = $request->has('limit') ? $request->limit : 10;

        $authorize_setting = AuthorizeSetting::select('id', 'name', 'status', 'login_id', 'currency', 'payment_API', 'payment_account_validation_mode', 'test_mode', 'developer_mode', 'is_default')
            ->latest()
            ->paginateData($limit);

        foreach ($authorize_setting as $key) {
            if ($key['status'] === 'I') {
                $key['status'] = false;
            } elseif ($key['status'] === 'A') {
                $key['status'] = true;
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'authorize' => $authorize_setting,
            'authorizationTotalCount' => AuthorizeSetting::where('creator_id', '=', $user_id)->count(),
        ], "message" => "Authorize.Net setting Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net setting Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'authorize' => $authorize_setting,
            'authorizationTotalCount' => AuthorizeSetting::where('creator_id', '=', $user_id)->count(),
        ]);
    }

    public function store(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //// Obtener el registro del usuario logueado
        $user_id = Auth::user()->id;

        //// Buscando otros registros con status activo
        $isStatus = AuthorizeSetting::where('status', 'A')->where('creator_id', '=', $user_id)->first();

        //// Si hay registros se cambian a inactivo (solo puede haber uno activo)
        if ($isStatus) {
            $isStatus->status = 'I';
            $isStatus->save();
        }

        // Crear opciones de pago
        $authorize_setting = new AuthorizeSetting();
        $authorize_setting->login_id = $request->input('login_id');
        $authorize_setting->name = $request->input('name');
        $authorize_setting->transaction_key = $request->input('transaction_key');
        $authorize_setting->currency = $request->input('currency');
        $authorize_setting->payment_API = $request->input('payment_API');
        // $authorize_setting->payment_account_validation_mode = $request->input('payment_account_validation_mode');
        $authorize_setting->test_mode = $request->input('test_mode');
        $authorize_setting->developer_mode = $request->input('developer_mode');
        $authorize_setting->status = $request->input('status');
        $authorize_setting->company_id = Auth::user()->company_id;
        $authorize_setting->creator_id = Auth::user()->id;

        $authorize_setting->enable_identification_verification = $request->input('enable_identification_verification');
        $authorize_setting->enable_fee_charges = $request->input('enable_fee_charges');
        $authorize_setting->save();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'authorize' => $authorize_setting,
        ], "message" => "Authorize.Net setting store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net setting store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'authorize' => $authorize_setting,
        ]);

    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\AuthorizeSetting  $authorizeSetting
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //// Obtener el registro de authorize
        $authorize_setting = AuthorizeSetting::where('id', $id)->first();

        switch ($authorize_setting['payment_API']) {
            case 'CIM':
                $authorize_setting['payment_API'] = ['value' => 'CIM', 'text' => 'CIM (must be enabled by Authorize.Net)'];

                break;

            case 'AIM':
                $authorize_setting['payment_API'] = ['value' => 'AIM', 'text' => 'AIM (default)'];

                break;

            default:
                break;
        }

        switch ($authorize_setting['payment_account_validation_mode']) {
            case 'none':
                $authorize_setting['payment_account_validation_mode'] = ['value' => 'none', 'text' => 'None'];

                break;

            case 'test':
                $authorize_setting['payment_account_validation_mode'] = ['value' => 'test', 'text' => 'Test'];

                break;

            case 'live':
                $authorize_setting['payment_account_validation_mode'] = ['value' => 'live', 'text' => 'Live'];

                break;

            default:
                break;
        }

        switch ($authorize_setting['currency']) {
            case 'USD':
                $authorize_setting['currency'] = ['value' => 'USD', 'text' => 'USD'];

                break;

            case 'EUR':
                $authorize_setting['currency'] = ['value' => 'EUR', 'text' => 'EUR'];

                break;

            case 'CAD':
                $authorize_setting['currency'] = ['value' => 'CAD', 'text' => 'CAD'];

                break;

            case 'GBP':
                $authorize_setting['currency'] = ['value' => 'GBP', 'text' => 'GBP'];

                break;

            default:
                break;
        }

        switch ($authorize_setting['status']) {
            case 'A':
                $authorize_setting['status'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $authorize_setting['status'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            default:
                break;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'authorize' => $authorize_setting,
        ], "message" => "Authorize.Net setting store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net setting show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'authorize' => $authorize_setting,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\AuthorizeSetting  $authorizeSetting
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "update");
        ////////////////

        //// Obtener el registro del usuario logueado
        $user_id = Auth::user()->id;

        //// Buscando otros registros con status activo
        $isStatus = AuthorizeSetting::where('id', '!=', $id)->where('status', 'A')->where('creator_id', '=', $user_id)->first();

        //// Si hay registros se cambian a inactivo (solo puede haber uno activo)
        if ($isStatus) {
            $isStatus->status = 'I';
            $isStatus->save();
        }

        $authorize_setting = AuthorizeSetting::find($id);
        $authorize_setting->login_id = $request->input('login_id');
        $authorize_setting->name = $request->input('name');
        $authorize_setting->transaction_key = $request->input('transaction_key');
        $authorize_setting->currency = $request->input('currency');
        $authorize_setting->payment_API = $request->input('payment_API');
        $authorize_setting->payment_account_validation_mode = $request->input('payment_account_validation_mode');
        $authorize_setting->test_mode = $request->input('test_mode');
        $authorize_setting->developer_mode = $request->input('developer_mode');
        $authorize_setting->status = $request->input('status');
        $authorize_setting->enable_identification_verification = $request->input('enable_identification_verification');
        $authorize_setting->enable_fee_charges = $request->input('enable_fee_charges');
        $authorize_setting->save();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'authorize' => $authorize_setting,
        ], "message" => "Authorize.Net setting update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net setting update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'authorize' => $authorize_setting,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\AuthorizeSetting  $authorizeSetting
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "destroy");
        //////////////////

        AuthorizeSetting::destroy($request->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Authorize.Net setting deleted successfully',
        ], "message" => "Authorize.Net setting deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Authorize.Net setting deleted successfully',
        ]);
    }

    public function changeStatus(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "changeStatus");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $count = AuthorizeSetting::count();

        for ($i = 0; $i < $count; $i++) {
            $id = $request[$i]["id"];
            $authorize_setting = AuthorizeSetting::find($id);
            if ($request[$i]["status"] == true) {
                $authorize_setting->status = 'A';
            }
            if ($request[$i]["status"] == false) {
                $authorize_setting->status = 'I';
            }
            $authorize_setting = $authorize_setting->save();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            // 'authorize' => $authorize_setting,
        ], "message" => "Authorize.Net store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net changeStatus");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'authorize_setting' => $authorize_setting,
        ]);

    }

    /**
     * update status and defaut the specified resource from storage.
     *
     * @param  \Crater\Models\avalara_config  $avalara_config
     * @return \Illuminate\Http\Response
     */
    public function setAuthorizeDefault(Request $request, $id)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "AuthorizeSettingsController", "setAuthorizeDefault");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // //Log::debug('request');
        // //Log::debug($request);

        DB::update('
            UPDATE authorize_settings SET is_default = ?, status = ? WHERE id != ?
        ', [0, 'I', $id]);

        $authorize_setting = AuthorizeSetting::find($id);
        $authorize_setting->status = 'A';
        $authorize_setting->is_default = 1;
        // $avalara_config->save();

        $updated = $authorize_setting->save();

        if ($updated) {
            $response = [
                'status' => 200,
                'response' => 'Authorize setting update correctly',
                'success' => true,
            ];
        } else {
            $response = [
                'status' => 406,
                'response' => 'Error saving Authorize setting',
                'success' => false,
            ];
        }

        LogsDev::finishLog($log, $response, $time, 'D', "AuthorizeSettingsController setAuthorizeDefault");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("AuthorizeSettingsController", "setAuthorizeDefault", "admin/authorize-settings/set-default/{id}", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json($response, $response['status']);
    }

    public function checkAuthorize()
    {
        $authorizeSetting = AuthorizeSetting::where('status', 'A')
            ->where('is_default', 1)
            ->first();

        $result = $authorizeSetting ? 'YES' : 'NO';

        $response = [
            'status' => 200,
            'result' => $result,
            'success' => true,
        ];

        return response()->json($response, $response['status']);
    }
}
