<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class ChangeInvoiceStatusController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Invoice $invoice)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['Invoice' => $invoice]);
        $log = LogsDev::initLog($request, "", "D", "ChangeInvoiceStatusController", "__invoke");
        //////////////////

        if ($request->status == Invoice::STATUS_SENT) {
            $invoice->status = Invoice::STATUS_SENT;
            $invoice->save_as_draft = false;
            $invoice->sent = true;
            $invoice->save();
        } elseif ($request->status == Invoice::STATUS_COMPLETED) {
            $invoice->status = Invoice::STATUS_COMPLETED;
            $invoice->paid_status = Invoice::STATUS_PAID;
            $invoice->due_amount = 0;
            $invoice->save();
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "Invoice v__invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoice v__invoke");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "Update", "admin/invoices", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice Change Status: ".$invoice->invoice_number);


        return response()->json([
            'success' => true,
        ]);
    }
}
