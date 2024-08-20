<?php

namespace Crater\Http\Controllers\V1\Payment;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\FailedPaymentHistoryRequest;
use Crater\Models\FailedPaymentHistory;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;
use Log;

class FailedPaymentHistoryController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "FailedPaymentHistoryController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $limit = $request->has('limit') ? $request->limit : 10;
        $failed_payment_history = FailedPaymentHistory::applyFilters($request->only([
                'search',
                'payment_number',
                'invoice_number',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy',
                'payment_gateway',
                'customer',
                'customerId'
            ]))
            ->with(['invoice'])
            ->join('users', 'failed_payment_history.customer_id', 'users.id')
            ->join('currencies', 'users.currency_id', 'currencies.id')
            ->leftJoin('invoices', 'failed_payment_history.invoice_id', 'invoices.id')
            ->select(
                'failed_payment_history.*',
                'invoices.invoice_number as invoice_number',
                'users.company_name',
                'users.name',
                'currencies.symbol',
                'currencies.precision',
                'currencies.decimal_separator',
                'currencies.thousand_separator'
            )
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'failed_payment_history' => $failed_payment_history,
            'failedPaymentHistoryTotalCount' => FailedPaymentHistory::count(),
        ], "message" => "index Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "index FailedPaymentHistory");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Payments", "List", "admin/failed-payment-history", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'failed_payment_history' => $failed_payment_history,
            'failedPaymentHistoryTotalCount' => FailedPaymentHistory::count(),
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
    public function store(FailedPaymentHistoryRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "FailedPaymentHistoryController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        // $authorize = $request->authorize;
        // return $authorize;

        $failed_payment_history = FailedPaymentHistory::createFailedPaymentHistory($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'failed_payment_history' => $failed_payment_history,
            'success' => true,
        ], "message" => " store FailedPaymentHistory"];
        LogsDev::finishLog($log, $res, $time, 'D', "  store FailedPaymentHistory");
        /////////////////////////////////////////



        // Logs por modulo
        // LogsModule::createLog("FailedPaymentHistory", "Create", "admin/failed-payment-history/create", $failed_payment_history->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        // envio de correo electronico
        // ver que informacion llega en el request
        try {
            $failed_payment_history->paymentFailed($request->all());
        } catch (\Throwable $th) {
            //throw $th;
        }

        return response()->json([
            'failed_payment_history' => $failed_payment_history,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
