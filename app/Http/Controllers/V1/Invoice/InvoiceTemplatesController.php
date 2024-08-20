<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Crater\Http\Controllers\Controller;
use Crater\Models\InvoiceTemplate;
use Crater\Models\LogsDev;
//// Models
use Illuminate\Http\Request;

class InvoiceTemplatesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoiceTemplatesController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $invoiceTemplates = InvoiceTemplate::all();

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'invoiceTemplates' => $invoiceTemplates,
        ], "message" => "Invoices __invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices __invoke");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,


        return response()->json([
            'invoiceTemplates' => $invoiceTemplates,
        ]);
    }
}
