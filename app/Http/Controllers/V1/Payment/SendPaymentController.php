<?php

namespace Crater\Http\Controllers\V1\Payment;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\SendPaymentRequest;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Illuminate\Http\Request;

class SendPaymentController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendPaymentRequest $request, Payment $payment)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "SendPaymentController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $response = $payment->send($request->all());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => $response, "message" => "__invoke Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "__invoke Payments");

        // Logs por modulo
        LogsModule::createLog("Payments", "Send", "admin/payments", $payment->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Payment: ".$payment->payment_number);

        /////////////////////////////////////////

        return response()->json($response);
    }
}
