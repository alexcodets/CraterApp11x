<?php

namespace Crater\Http\Controllers\V1\Settings;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\PaymentGateways;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class PaymentGatewayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPaymentGateways(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewayController", "getPaymentGateways");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        if(! empty($request)) {
            $orderByField = 'id';
            $orderBy = 'desc';
        } else {
            $orderByField = $request['orderByField'];
            $orderBy = $request['orderBy'];
        }

        // consulta
        $payment_gateways = PaymentGateways::orderBy($orderByField, $orderBy)->get();

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_gateways' => $payment_gateways,
        ], "message" => "Listado de pasarelas de pago"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado de pasarelas de pago");

        return response()->json([
            'payment_gateways' => $payment_gateways
        ]);

    }

    public function getPaymentGatewaysAch(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewayController", "getPaymentGateways");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        if(! empty($request)) {
            $orderByField = 'id';
            $orderBy = 'desc';
        } else {
            $orderByField = $request['orderByField'];
            $orderBy = $request['orderBy'];
        }

        // consulta
        $payment_gateways = PaymentGateways::orderBy($orderByField, $orderBy)->where('name', '!=', 'Paypal')->get();

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_gateways' => $payment_gateways,
        ], "message" => "Listado de pasarelas de pago"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado de pasarelas de pago");

        return response()->json([
            'payment_gateways' => $payment_gateways
        ]);

    }

    public function gatewaysIndex(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewayController", "getPaymentGateways");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        $orderByField = $request['orderByField'];
        $orderBy = $request['orderBy'];

        Log::debug('request', ['request' => $request->all()]);
        // consulta bd
        $payment_gateways = DB::table('payment_gateways')
        ->orderBy($orderByField, $orderBy)->get();

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'payment_gateways' => $payment_gateways,
        ], "message" => "Listado de pasarelas de pago"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Listado de pasarelas de pago");

        return response()->json([
            'payment_gateways' => $payment_gateways
        ]);

    }

    public function changeStatus(Request $request, $id)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewayController", "changeStatus");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //// Obtener el registro de authorize.Net del usuario logueado
        // $payment_gateway = PaymentGateways::where('id', $id)->first();
        $payment_gateway = PaymentGateways::find($id);

        //// Cambiar status
        if ($payment_gateway->status == 'A') {
            $payment_gateway->status = 'I';
        } elseif ($payment_gateway->status == 'I') {
            $payment_gateway->status = 'A';
        }

        $payment_gateway = $payment_gateway->save();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            'authorize' => $payment_gateway,
        ], "message" => "Authorize.Net store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net changeStatus");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'payment_gateway' => $payment_gateway
        ]);

    }

    public function changeDefault(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "PaymentGatewayController", "changeDefault");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $count = PaymentGateways::count();

        for ($i = 0; $i < $count; $i++) {
            $id = $request[$i]["id"];
            $payment_gateway = PaymentGateways::find($id);
            $payment_gateway->default = $request[$i]["default"];
            $payment_gateway = $payment_gateway->save();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return
        $res = ["success" => true, "response" => ["datamesage" => [
            // 'authorize' => $payment_gateway,
        ], "message" => "Authorize.Net store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Authorize.Net changeDefault");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return

        return response()->json([
            'payment_gateway' => $payment_gateway
        ]);

    }
}
