<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
//// Models
use Crater\Models\Payment;
use Crater\Models\User;
use Illuminate\Http\Request;

class InvoiceCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "index");
        $usercurrent = User::where("id", Auth::id())->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //Log::debug("entro a index invoices cutsomter");
        $limit = $request->input('limit', 10);
        $totalopen = 0;

        if ($usercurrent != null && $usercurrent->role == "customer") {
            //  //Log::debug($request->input());
            $totalopen = Invoice::where('user_id', $usercurrent->id)
                        ->where('company_id', $usercurrent->company_id)
                         ->whereNotIn('status', ['DRAFT', 'SAVE_DRAFT'])
                         ->whereNull('deleted_at')->count();


            $invoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
                ->applyFilters($request->only([
                    'status',
                    'paid_status',
                    'customer_id',
                    'invoice_id',
                    'invoice_number',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy',
                    'search',
                ]))
                ->where('company_id', $usercurrent->company_id)
                ->where('user_id', $usercurrent->id)
                ->whereNotIn('status', ['DRAFT', 'SAVE_DRAFT'])
                ->whereNull('deleted_at')
                ->latest()
                ->paginateData($limit);
        } else {

            $invoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->applyFilters($request->only([
                    'status',
                    'paid_status',
                    'customer_id',
                    'invoice_id',
                    'invoice_number',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy',
                    'search',
                ]))
                ->whereCompany($request->header('company'))
                ->select('invoices.*', 'users.name')
                ->latest()
                ->paginateData($limit);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'invoices' => $invoices,
            'invoiceTotalCount' => Invoice::where('user_id', $usercurrent->id)->whereNull('deleted_at')->count(),
            'invoiceTotalOpen' => $totalopen,
        ], "message" => "Invoices index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "List", "admin/invoices", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'invoices' => $invoices,
            'invoiceTotalCount' => Invoice::count(),
            'invoiceTotalOpen' => $totalopen,
        ]);
    }

    public function show(Request $request, Invoice $invoice)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $time = microtime(true);
        $request->merge(['Invoice' => $invoice]);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $invoice->load([
            'items',
            'items.taxes',
            'user',
            'invoiceTemplate',
            'taxes.taxType',
            'fields.customField',
            'packages',
        ]);

        $listpay = Payment::where("invoice_id", $invoice->id)->where("transaction_status", "Approved")->get()->toarray();
        $siteData = [
            'invoice' => $invoice,
            'payments' => $listpay,
            'nextInvoiceNumber' => $invoice->getInvoiceNumAttribute(),
            'invoicePrefix' => $invoice->getInvoicePrefixAttribute(),
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $siteData, "message" => "Invoices show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "View", "admin/invoices/id/view", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: ".$invoice->invoice_number);

        return response()->json($siteData);
    }

    public function __invoke($hash)
    {
        $invoice = Invoice::withTrashed()
            ->where('unique_hash', $hash)
            ->first();

        return $invoice->getGeneratedPDFOrStream('invoice');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function indexByStatus(Request $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoiceCustomerController", "indexByStatus");
        $usercurrent = User::where("id", Auth::id())->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 10;

        // validating status parameter
        if (! $request->input('status_oc')) {
            $res = [
                "success" => false,
                "response" => [
                    "message" => "status_oc parameter is not set",
                ],
            ];

            LogsDev::finishLog($log, $res, $time, 'D', "Listado de customer invoices");

            return response()->json([
                'status' => 400,
                'success' => false,
                'message' => 'status_oc parameter is not set',
            ]);

        }

        if ($usercurrent != null && $usercurrent->role == "customer") {
            $invoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
                ->applyFilters($request->only([
                    'status',
                    'status_oc',
                    'paid_status',
                    'customer_id',
                    'invoice_id',
                    'invoice_number',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy',
                    'search',
                ]))
                ->where('company_id', $usercurrent->company_id)
                ->where('user_id', $usercurrent->id)
                ->where('status', "!=", "DRAFT")
                ->where("status", "!=", "SAVE_DRAFT")
                ->whereNull('deleted_at')
                ->latest()
                ->paginateData($limit);

        } else {

            $invoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->applyFilters($request->only([
                    'status',
                    'status_oc',
                    'paid_status',
                    'customer_id',
                    'invoice_id',
                    'invoice_number',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy',
                    'search',
                ]))
                ->whereCompany($request->header('company'))
                ->select('invoices.*', 'users.name')
                ->where('status', "!=", "DRAFT")
                ->where("status", "!=", "SAVE_DRAFT")
                ->whereNull('invoices.deleted_at')
                ->latest()
                ->paginateData($limit);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'invoices' => $invoices,
        ], "message" => "Invoices index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        // LogsModule::createLog("Invoices", "List", "admin/invoices", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'invoices' => $invoices,
        ]);
    }
}
