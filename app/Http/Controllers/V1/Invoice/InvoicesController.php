<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Auth;
use Carbon\Carbon;
use Crater\Helpers\Chronometer;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests;
use Crater\Http\Requests\DeleteInvoiceRequest;
use Crater\Jobs\GenerateInvoicePdfJob;
use Crater\Jobs\PbxAvalaraEditInvoiceJob;
use Crater\Jobs\PbxGenerateAvalaraTaxesJob;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerPackage;
use Crater\Models\Expense;
use Crater\Models\ExpenseCategory;
use Crater\Models\Invoice;

//// Models
use Crater\Models\InvoiceAdditionalCharge;
use Crater\Models\InvoiceAppRates;
use Crater\Models\InvoiceCustomerPackages;
use Crater\Models\InvoiceLateFee;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class InvoicesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $chrono = new Chronometer();
        $chrono->start('app');

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "index");
        $user = auth()->user();
        /* @var User $user */
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //$limit = $request->has('limit') ? $request->limit : 10;
        $limit = $request->input('limit', 10);

        //        $chrono->start('invoices');
        if ($user != null && $user->role == "customer") {

            $invoices = Invoice::with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->applyFilters($request->only([
                    'status',
                    'paid_status',
                    'customer_id',
                    'invoice_id',
                    'invoice_number',
                    'customcode',
                    'from_date',
                    'to_date',
                    'orderByField',
                    'orderBy',
                    'search',
                ]))
                ->whereCompany($user->company_id)
                ->where('invoices.user_id', $user->id)

                ->where(function ($query) {
                    $query->where('invoices.status', 'SENT')
                        ->orWhere('invoices.status', 'VIEWED')
                        ->orWhere('invoices.status', 'OVERDUE');
                })
                ->select('invoices.*', 'users.name')
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
                    'customcode',
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
        //        $chrono->end('invoices');
        //        $chrono->toLogTime('invoices');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        //$chrono->start('logs');

        $totalInvoice = Invoice::count();

        $res = ["success" => true, "response" => ["datamesage" => [
            'invoices' => $invoices,
            'invoiceTotalCount' => $totalInvoice,
        ], "message" => "Invoices index"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Invoices index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "List", "admin/invoices", 0, $user->name, $user->email, $user->role, $user->company_id);
        //        $chrono->end('logs');
        //        $chrono->toLogTime('logs');
        //
        $invoices = $invoices->toArray();
        $chrono->end('app');
        $chrono->toLogTime('app');

        return response()->json([
            'invoices' => $invoices,
            'invoiceTotalCount' => $totalInvoice,
        ]);
    }

    /**
     * Display a listing of the resource of paymenttall
     *
     * @return JsonResponse
     */
    public function indexforpayments(Request $request)
    {
        //$time = microtime(true);
        //$log = LogsDev::initLog($request, "", "D", "InvoicesController", "index");

        $user = Auth::user();
        $company_id = $user->company_id;

        $invoicesQuery = Invoice::with(['creator'])
            ->join('users', 'users.id', '=', 'invoices.user_id')
            ->applyFilters($request->only(['customer_id', 'invoice_id']))
            ->whereNotIn('invoices.status', ['DRAFT', 'SAVE_DRAFT', 'COMPLETED'])
            ->where('invoices.paid_status', '!=', 'PAID')
            ->where('invoices.company_id', $company_id)
            ->orderBy('invoices.due_amount', 'desc') // Ordenar por due_amount de mayor a menor
            ->orderBy('invoices.due_date', 'asc'); // Ordenar por due_date de más antiguo a más nuevo

        if ($user->role == "customer") {
            $invoicesQuery->where('invoices.user_id', $user->id);
        }

        $invoices = $invoicesQuery->select('invoices.*', 'users.name')
            ->latest()
            ->paginateData(1000);

        $invoiceTotalCount = $invoicesQuery->count();

        // Fin de registro de log

        // Logs por modulo
        LogsModule::createLog("Invoices", "List", "admin/invoices", 0, $user->name, $user->email, $user->role, $company_id);

        return response()->json([
            'invoices' => $invoices,
            'invoiceTotalCount' => $invoiceTotalCount,
        ]);
    }

    public function fetchInvoicesCustomerPayments(Request $request)
    {
        $invoices = Invoice::where("user_id", $request['params']['customer_id'])
            ->whereNotIn('status', ['DRAFT', 'SAVE_DRAFT', 'COMPLETED'])
            ->where('paid_status', "!=", "PAID")
            ->get();

        return response()->json([
            'invoices' => $invoices,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function indexArchived(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "index");
        $usercurrent = User::where("id", Auth::id())->first();
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->input('limit', 10);

        if ($usercurrent != null && $usercurrent->role == "customer") {
            $invoices = Invoice::onlyTrashed()
                ->with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes', 'avalaraInvoice'])
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
                ->whereCompany($usercurrent->company_id)
                ->where('invoices.user_id', Auth::id())
                ->select('invoices.*', 'users.name')
                ->latest()
                ->paginateData($limit);
        } else {
            $invoices = Invoice::onlyTrashed()
                ->with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes', 'avalaraInvoice'])
                ->join('users', 'users.id', '=', 'invoices.user_id')
                ->applyFilters($request->only([
                    //'status',
                    'paid_status',
                    'customer_id',
                    'invoice_id',
                    'invoice_number',
                    'customcode',
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
            'invoiceTotalCount' => Invoice::onlyTrashed()->count(),
        ], "message" => "Invoices index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices index");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "List", "admin/invoices", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'invoices' => $invoices,
            'invoiceTotalCount' => Invoice::onlyTrashed()->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function store(Requests\InvoicesRequest $request)
    {
        // \Log::debug($request);
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $invoice = Invoice::createInvoice($request);

        if (count($request->packages) > 0) {
            $this->addInvoiceCustomerPackage(collect($request->packages), $invoice->id, $request->input('user_id'), Auth::user()->company_id, );
        }

        if ($request->has('invoiceSend')) {
            $invoice->send($request->subject, $request->body);
        }

        GenerateInvoicePdfJob::dispatch($invoice);

        //if ($invoice->inv_avalara_bool){
        //$this->invoice->inv_avalara_bool
        try {

            if ($invoice->pbx_service_id != null && $invoice->pbxService->pbxPackage->avalara_options) {
                PbxGenerateAvalaraTaxesJob::dispatchSync($invoice->id);
            }
            if ($invoice->pbx_service_id == null && $invoice->inv_avalara_bool) {
                PbxGenerateAvalaraTaxesJob::dispatchSync($invoice->id, '--is_manual_invoice');
            }

        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error While creating Sync Avalara Taxes:');
            Log::error($th->getMessage());
            if ($invoice->pbx_service_id != null && $invoice->pbxService->pbxPackage->avalara_options) {
                PbxGenerateAvalaraTaxesJob::dispatchSync($invoice->id);
            }
            if ($invoice->pbx_service_id == null && $invoice->inv_avalara_bool) {
                PbxGenerateAvalaraTaxesJob::dispatchSync($invoice->id, '--is_manual_invoice');
            }
        }
        //}
        // Send email
        if ($request->has('send_email')) {
            $this->sendEmail($request, $invoice);
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'invoice' => $invoice,
        ], "message" => "Invoices store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices store");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "Create", "admin/invoices/create", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: ".$invoice->invoice_number);

        return response()->json([
            'invoice' => $invoice,
        ]);
        //
    }

    public function sendEmail($request, $invoice)
    {
        if (! $request->send_email || config('mail.from.address') == null) {
            return;
        }

        try {
            $companyId = Auth::user()->company_id;
            $settings = CompanySetting::where('company_id', $companyId)
                ->whereIn('option', ['invoice_mail_subject', 'invoice_mail_body'])
                ->get()
                ->keyBy('option'); // Esto agrupa los resultados por la opción

            $emailSubject = $settings->get('invoice_mail_subject');
            $emailBody = $settings->get('invoice_mail_body');
            $to = User::where('id', $request->user_id)->value('email'); // Usar value en lugar de pluck cuando se espera un solo valor

            if ($emailSubject && $emailBody && $to) {
                $data = [
                    'subject' => $emailSubject->value,
                    'body' => $emailBody->value,
                    'to' => $to,
                    'from' => config('mail.from.address'),
                ];
                $invoice->send($data);
            }
        } catch (\Throwable $th) {
            \Log::error("Error sending email: ".$th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Invoice $invoice
     * @return JsonResponse
     */
    public function show(Request $request, $id)
    {
        // Log::debug('llegó--------------------');
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        //Log::debug( $id);
        if ($id == null) {

            return false;
        }
        $time = microtime(true);

        $invoice = Invoice::withTrashed()->find($id);

        if ($invoice == null) {

            return false;
        }

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
            'serviceDetails',
            'InvoicePbxExtensionDetail',
            'invoicePbxDidDetail',
        ]);

        $posTip = DB::table('pos_tip')->where('invoice_id', $invoice->id)->first();

        if (! empty($posTip)) {
            $invoice['tip'] = $posTip->tip;
            $invoice['tip_val'] = $posTip->tip_val;
            $invoice['tip_type'] = $posTip->tip_type;
        }
        $listpay = Payment::where("invoice_id", $id)->where("transaction_status", "Approved")->get()->toarray();
        $invoice->inv_addtional_char = InvoiceAdditionalCharge::where("invoice_id", $id)->get()->toarray();
        $codeservice = null;

        if ($invoice->customer_packages_id != null) {
            $customerpackage = CustomerPackage::where("id", $invoice->customer_packages_id)->first();
            if ($customerpackage != null) {
                $codeservice = $customerpackage->code;
            }
        }

        if ($invoice->pbx_service_id != null) {
            $customerpackage = PbxServices::where("id", $invoice->pbx_service_id)->first();
            if ($customerpackage != null) {
                $codeservice = $customerpackage->pbx_services_number;
            }
        }

        // Parse date (Invoice)
        $parse_invoice_date = Carbon::parse($invoice['invoice_date'])->format('Y-m-d');
        $invoice['parse_invoice_date'] = $parse_invoice_date;
        $parse_due_date = Carbon::parse($invoice['due_date'])->format('Y-m-d');
        $invoice['parse_due_date'] = $parse_due_date;
        //

        $siteData = [
            'invoice' => $invoice,
            'payments' => $listpay,
            'nextInvoiceNumber' => $invoice->getInvoiceNumAttribute(),
            'invoicePrefix' => $invoice->getInvoicePrefixAttribute(),
            'codeservice' => $codeservice,
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $siteData, "message" => "Invoices show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "View", "admin/invoices/id/view", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: ".$invoice->invoice_number);

        return response()->json($siteData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Invoice $invoice
     * @return JsonResponse
     */
    public function showArchived(Request $request, $idInvoice)
    {
        //Log::debug('llegó Archived --------------------');
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $time = microtime(true);
        // $request->merge(['Invoice' => $invoice]);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $invoice = Invoice::onlyTrashed()
            ->with(['items', 'user', 'creator', 'invoiceTemplate', 'taxes'])
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
            ->where('invoices.id', $idInvoice)
            ->get();

        //  Log::debug($invoice);
        $invoice = $invoice[0];
        $siteData = [
            'invoice' => $invoice,
        ];

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => $siteData, "message" => "Invoices show"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices show");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "View", "admin/invoices/id/view", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: ".$invoice->invoice_number);

        return response()->json($siteData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Invoice $invoice
     * @return JsonResponse
     */
    public function update(Requests\InvoicesRequest $request, Invoice $invoice)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Invoice' => $invoice]);
        $log = LogsDev::initLog($request2, "", "D", "InvoicesController", "update");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        //Old due_amount and total (Late Fees)
        $old_invoice = Invoice::find($invoice->id);
        $old_due_amount = $old_invoice->due_amount;
        $old_total = $old_invoice->total;
        //

        \Log::debug("actualizacion modelo");
        \Log::debug($request->input());
        $invoice = $invoice->updateInvoice($request);

        \Log::debug("actualizacion despues modelo");
        \Log::debug($invoice);
        ///////////////////////////////////////////////////////////////////////////////////
        // Late Fees update / Delete
        if (count($request["invoice_late_fees"]) > 0) {
            $delete_late_fees = InvoiceLateFee::where('invoice_id', $invoice->id)
                ->where('deleted_at', null)
                ->update(['deleted_at' => Carbon::now()]);

            foreach ($request["invoice_late_fees"] as $inv_late_fee) {
                $late_fee = InvoiceLateFee::find($inv_late_fee["id"]);

                if ($late_fee) {
                    $late_fee->subtotal = $inv_late_fee["subtotal"];
                    $late_fee->tax_amount = $inv_late_fee["tax_amount"];
                    $late_fee->total = $inv_late_fee["total"];
                    $late_fee->deleted_at = null;
                    $late_fee->save();
                }
            }
        } else {
            $delete_late_fees = InvoiceLateFee::where('invoice_id', $invoice->id)
                ->where('deleted_at', null)
                ->update(['deleted_at' => Carbon::now()]);
        }
        //Update DueAmount and Total (late fees)
        $invoice = Invoice::find($invoice->id);



        \Log::debug("despues de late feeo");
        \Log::debug($invoice);
        if (count($request->packages) > 0) {
            InvoiceCustomerPackages::where('invoice_id', $request->input('id'))->delete();
            $this->addInvoiceCustomerPackage(collect($request->packages), $invoice->id, $request->input('user_id'), Auth::user()->company_id, );
        }

        GenerateInvoicePdfJob::dispatch($invoice, true);

        try {

            if ($invoice->pbx_service_id != null && $invoice->pbxService->pbxPackage->avalara_options) {
                PbxAvalaraEditInvoiceJob::dispatchSync($invoice->id);
            }
            if ($invoice->pbx_service_id == null && $invoice->inv_avalara_bool) {
                PbxAvalaraEditInvoiceJob::dispatchSync($invoice->id, '--is_manual_invoice');
            }

        } catch (\Throwable $th) {
            //throw $th;
            Log::debug('Error While creating Sync Avalara Taxes:');
            Log::debug($th->getMessage());
        }

        // Send email
        if ($request->has('send_email')) {
            $this->sendEmail($request, $invoice);
        }

        \Log::debug("despues de avalara");
        \Log::debug($invoice);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'invoice' => $invoice,
            'success' => true,
        ], "message" => "Invoices update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices update");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // crear registro de expense si acaso el monto de los pagos realizados supera a monto total de la factura

        $validatePayments = Payment::where('invoice_id', $invoice->id)->where('inv_expense_credit', 0)->get();
        if (! $validatePayments->isEmpty()) {
            Log::debug('Is valid Payment');
            $amountPayments = $validatePayments->sum('amount');
            $totalInvoice = $invoice->total;

            try {
                if ($amountPayments > $totalInvoice) {
                    $amountExcess = $amountPayments - $totalInvoice;
                    // crear expense
                    $expCategory = ExpenseCategory::where('company_id', $invoice->company_id)->where('for_payments', 1)->first();
                    if (! is_null($expCategory)) {
                        Expense::create([
                            'expense_category_id' => $expCategory->id,
                            'user_id' => Auth()->user()->id,
                            'company_id' => $invoice->company_id,
                            'amount' => $amountExcess,
                            'creator_id' => Auth()->user()->id ?? $invoice->creator_id,
                            'notes' => "Expense created from invoice number $invoice->invoice_number for concept of credit with this amount $ ".number_format(($amountExcess / 100), 2, ".", ","),
                        ]);
                    } else {
                        // crear nota en la factura sobre el excedente de los pagos realizados en caso de que el cliente no tenga categorias de gastos
                        $invoice->notes = $invoice->notes." Can't added excess credit for $".number_format(($amountExcess / 100), 2, ".", ",").", because the expense category for credit doesn't exist";
                        $invoice->save();
                    }
                    $user = User::where('id', Auth()->user()->id)->first();
                    $user->balance = $user->balance + ($amountExcess / 100);
                    $user->save();

                    foreach ($validatePayments as $payment) {
                        $payment->inv_expense_credit = 1;
                        $payment->save();
                    }

                }
            } catch (\Throwable $th) {
                Log::error('Error created of the expense if the total payments excess the total invoice', ['error' => $th]);
            }
        }
        // Logs por modulo
        LogsModule::createLog("Invoices", "Update", "admin/invoices/id/edit", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: ".$invoice->invoice_number);

        return response()->json([
            'invoice' => $invoice,
            'success' => true,
        ]);
    }

    public function restoreInvoice(Request $request)
    {
        // Inicio de registro de log
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "restoreInvoice");

        try {
            $invoice = Invoice::withTrashed()->find($request->id);
            $success = $invoice && $invoice->restore();

            // Registro de log por módulo
            LogsModule::createLog("Invoices", "Update", "admin/invoices/id/archived", $request->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: ".$invoice->invoice_number);

            // Fin de registro de log
            $res = ["success" => $success, "response" => ["datamesage" => ['invoice' => $request->id, 'success' => $success]], "message" => "Invoices update"];
            LogsDev::finishLog($log, $res, $time, 'D', "Invoices Archived Restored");

            return response()->json(['success' => $success]);
        } catch (\Throwable $th) {
            \Log::error("Error restoring invoice: ".$th->getMessage());

            return response()->json(['success' => false], 500);
        }
    }

    /**
     * delete the specified resources in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return JsonResponse
     */
    public function delete(DeleteInvoiceRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "InvoicesController", "index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        /* Lectura de ID de Invoices para los items
        foreach ($request->ids as $id) {
        $invoice = Item::find($id);
        // Logs por modulo
        LogsModule::createLog("Invoices", "delete", "admin/invoices/delete", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: " . $invoice->invoice_number);
        }
         */

        foreach ($request->ids as $id) {
            $Invoice = Invoice::find($id);
            if ($Invoice) {
                $Invoice->delete();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe el registro',
                ]);
            }
        }

        // Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => [
            'success' => true,
        ], "message" => "soft delete success"];
        LogsDev::finishLog($log, $res, $time, 'D', "Invoices soft delete success");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Registro eliminado con éxito.',
        ]);
    }

    public function addInvoiceCustomerPackage($packages, $invoice_id, $customer_id, $company_id)
    {
        $res = [];
        $packages->each(function ($item) use ($res, $invoice_id, $customer_id, $company_id) {
            $newInvoiceCustomerPackages = new InvoiceCustomerPackages();
            $newInvoiceCustomerPackages->customer_id = $customer_id;
            $newInvoiceCustomerPackages->invoice_id = $invoice_id;
            $newInvoiceCustomerPackages->package_id = $item['id'];
            $newInvoiceCustomerPackages->company_id = $company_id;
            $res[] = $newInvoiceCustomerPackages->save();
        });

        return $res;
    }

    public function ViewInvoiceUniqueHash(Request $request, $unique_hash)
    {

        $time = microtime(true);

        // invoiceIsNotExist
        $invoiceIsDeleted = Invoice::where('unique_hash', $unique_hash)->where("deleted_at", '=', null)->first();
        if ($invoiceIsDeleted == null) {
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found',
                'invoice' => null,
            ]);
        }
        //

        $idInvoice = Invoice::where('unique_hash', $unique_hash)->first()->id;
        /*
        if (!$idInvoice) {
        return response()->json([
        'success' => false,
        'message' => 'Invoice not found'
        ], 404);
        }
         */
        $invoice = Invoice::withTrashed()->find($idInvoice);

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
            'serviceDetails',
        ]);

        $listpay = Payment::where("invoice_id", $idInvoice)->where("transaction_status", "Approved")->get()->toarray();

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
        // LogsModule::createLog("Invoices", "View", "admin/invoices/id/view", $invoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice: " . $invoice->invoice_number);

        return response()->json($siteData);
    }

    public function getAppsRatesInvoice($invoiceId)
    {

        $data = InvoiceAppRates::where('invoice_id', $invoiceId)->get();

        if ($data->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "Apps Rates not found",
                "data" => [],
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Invoice App Rates",
            "data" => $data,
        ]);
    }

    public function getInvoiceLateFees($id)
    {
        $invoice_late_fees = InvoiceLateFee::where('invoice_id', $id)->where("deleted_at", null)->get();

        if ($invoice_late_fees->isEmpty()) {
            return response()->json([
                "success" => false,
                "message" => "Invoice late fees not found",
            ]);
        }

        return response()->json([
            "success" => true,
            "message" => "Invoice App Rates",
            "invoice_late_fees" => $invoice_late_fees,
        ]);

    }
}
