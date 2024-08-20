<?php

namespace Crater\Http\Controllers\V1\Paypal;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeletePaypalSettingRequest;
use Crater\Http\Requests\PaypalSettingRequest;
use Crater\Models\LogsDev;
use Crater\Models\PaypalSetting;
use Illuminate\Http\Request;

class PaypalSettingsController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "PaypalSettingsController", "index");
        ////////////////
        $user_id = Auth::user()->id;

        $limit = $request->has('limit') ? $request->limit : 10;

        $paypal_setting = PaypalSetting::applyFilters($request->only([
            'search',
            'status',
            'merchant_id',
            'public_key',
            'email',
            'orderByField',
            'orderBy',
        ]))
        ->where('creator_id', '=', $user_id)
        ->select('id', 'status', 'merchant_id', 'currency', 'email', 'public_key', 'enviroment', 'name')
        ->latest()
        ->paginateData($limit);

        foreach ($paypal_setting as $key) {
            if ($key['status'] === 'I') {
                $key['status'] = false;
            } elseif ($key['status'] === 'A') {
                $key['status'] = true;
            }
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'paypal' => $paypal_setting,
            'paypalTotalCount' => PaypalSetting::where('creator_id', '=', $user_id)->count(),
        ], "message" => "Paypal setting Index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal setting Index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'paypal' => $paypal_setting,
            'paypalTotalCount' => PaypalSetting::where('creator_id', '=', $user_id)->count(),
        ]);
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
    public function store(PaypalSettingRequest $request)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaypalSettingController", "store");
        ////////////////

        $user_id = Auth::user()->id;

        //// Buscando otros registros con status activo
        $isStatus = PaypalSetting::where('status', 'A')->where('creator_id', '=', $user_id)->first();

        //// Si hay registros se cambian a inactivo (solo puede haber uno activo)
        if ($isStatus) {
            $isStatus->status = 'I';
            $isStatus->save();
        }

        // Crear opciones de pago
        $paypal_setting = new PaypalSetting();
        // $paypal_setting->paypal_id = $request->input('paypal_id');
        $paypal_setting->email = $request->input('email');
        $paypal_setting->currency = $request->input('currency');
        // $paypal_setting->paypal_secret = $request->input('paypal_secret');
        // $paypal_setting->paypal_signature = $request->input('paypal_signature');
        // $paypal_setting->test_mode = $request->input('test_mode');
        // $paypal_setting->developer_mode = $request->input('developer_mode');
        $paypal_setting->merchant_id = $request->input('merchant_id');
        $paypal_setting->name = $request->input('name');
        $paypal_setting->public_key = $request->input('public_key');
        $paypal_setting->private_key = $request->input('private_key');
        $paypal_setting->enviroment = $request->input('enviroment');
        $paypal_setting->status = $request->input('status');
        $paypal_setting->enable_identification_verification = $request->input('enable_identification_verification');
        $paypal_setting->enable_fee_charges = $request->input('enable_fee_charges');
        $paypal_setting->creator_id = Auth::user()->id;
        $paypal_setting->save();


        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'paypal' => $paypal_setting,
        ], "message" => "Paypal setting store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal setting store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'paypal' => $paypal_setting
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaypalSettingsController", "show");
        ////////////////

        $paypal_setting = PaypalSetting::where('id', $id)->first();

        switch ($paypal_setting['currency']) {
            case 'USD':
                $paypal_setting['currency'] = ['value' => 'USD', 'text' => 'USD'];

                break;

            case 'EUR':
                $paypal_setting['currency'] = ['value' => 'EUR', 'text' => 'EUR'];

                break;

            case 'CAD':
                $paypal_setting['currency'] = ['value' => 'CAD', 'text' => 'CAD'];

                break;

            case 'GBP':
                $paypal_setting['currency'] = ['value' => 'GBP', 'text' => 'GBP'];

                break;

            default:
                break;
        }

        switch ($paypal_setting['status']) {
            case 'A':
                $paypal_setting['status'] = ['value' => 'A', 'text' => 'Active'];

                break;

            case 'I':
                $paypal_setting['status'] = ['value' => 'I', 'text' => 'Inactive'];

                break;

            default:
                break;
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'paypal' => $paypal_setting,
        ], "message" => "Paypal setting store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal setting show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'paypal' => $paypal_setting
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentAccount $paymentAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function update(PaypalSettingRequest $request, $id)
    {
        // Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "update");
        ////////////////

        //// Obtener el registro del usuario logueado
        $user_id = Auth::user()->id;

        //// Buscando otros registros con status activo
        $isStatus = PaypalSetting::where('id', '!=', $id)->where('status', 'A')->where('creator_id', '=', $user_id)->first();

        //// Si hay registros se cambian a inactivo (solo puede haber uno activo)
        if ($isStatus) {
            $isStatus->status = 'I';
            $isStatus->save();
        }

        $paypal_setting = PaypalSetting::find($id);
        // $paypal_setting->paypal_id = $request->input('paypal_id');
        $paypal_setting->email = $request->input('email');
        $paypal_setting->currency = $request->input('currency');
        // $paypal_setting->paypal_secret = $request->input('paypal_secret');
        // $paypal_setting->paypal_signature = $request->input('paypal_signature');
        // $paypal_setting->test_mode = $request->input('test_mode');
        // $paypal_setting->developer_mode = $request->input('developer_mode');
        $paypal_setting->name = $request->input('name');
        $paypal_setting->merchant_id = $request->input('merchant_id');
        $paypal_setting->public_key = $request->input('public_key');
        $paypal_setting->private_key = $request->input('private_key');
        $paypal_setting->enviroment = $request->input('enviroment');
        $paypal_setting->status = $request->input('status');

        $paypal_setting->enable_identification_verification = $request->input('enable_identification_verification');
        $paypal_setting->enable_fee_charges = $request->input('enable_fee_charges');
        $paypal_setting->creator_id = Auth::user()->id;
        $paypal_setting->save();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'paypal' => $paypal_setting,
        ], "message" => "Paypal setting update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal setting update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'paypal' => $paypal_setting
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\PaymentAccount  $paymentAccount
     * @return \Illuminate\Http\Response
     */
    public function delete(DeletePaypalSettingRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentAccountController", "destroy");
        //////////////////

        PaypalSetting::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => 'Paypal setting deleted successfully',
        ], "message" => "Paypal setting deleted successfully"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal deleted successfully");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'success' => 'Paypal setting deleted successfully',
        ]);
    }

    public function changeStatus(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaypalSettingsController", "changeStatus");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $count = PaypalSetting::count();

        for ($i = 0; $i < $count; $i++) {
            $id = $request[$i]["id"];
            $paypal_setting = PaypalSetting::find($id);
            if ($request[$i]["status"] == true) {
                $paypal_setting->status = 'A';
            }
            if ($request[$i]["status"] == false) {
                $paypal_setting->status = 'I';
            }
            $paypal_setting = $paypal_setting->save();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            // 'authorize' => $authorize_setting,
        ], "message" => "Paypal store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal changeStatus");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'paypal_setting' => $paypal_setting
        ]);

    }

    public function getPublicKeyPaypal(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaypalSettingsController", "getPublicKeyPaypal");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $paypalKeyId = PaypalSetting::where('status', 'A')->select('public_key', 'currency')->first();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'paypalKeyId' => $paypalKeyId,
        ], "message" => "Paypal getPublicKeyPaypal"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Paypal getPublicKeyPaypal");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'data' => $paypalKeyId
        ]);
    }
}
