<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\SendInvoiceRequest;
use Crater\Models\Invoice;
//// Models
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;

class SendInvoiceController extends Controller
{
    /**
     * Mail a specific invoice to the corresponding customer's email address.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(SendInvoiceRequest $request, Invoice $invoice)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Invoice' => $invoice]);
        $log = LogsDev::initLog($request2, "", "D", "InvoicesController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $invoice->send($request->all());

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "SendInvoice __invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "SendInvoice__invoke");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "Send", "admin/invoices", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice Change Status: ".$invoice->invoice_number);

        return response()->json([
            'success' => true,
        ]);
    }
}
