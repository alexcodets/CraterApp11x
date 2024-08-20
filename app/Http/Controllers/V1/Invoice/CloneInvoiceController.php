<?php

namespace Crater\Http\Controllers\V1\Invoice;

use Auth;
use Carbon\Carbon;
use Crater\Helpers\General;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CloneInvoiceController extends Controller
{
    /**
     * Mail a specific invoice to the corresponding customer's email address.
     *
     * @param Request $request
     * @param Invoice $invoice
     * @return JsonResponse
     */
    public function __invoke(Request $request, Invoice $invoice): JsonResponse
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Invoice' => $invoice]);
        $log = LogsDev::initLog($request2, "", "D", "CloneInvoiceController", "__invoke");
        //////////////////

        $date = Carbon::now();

        $invoice_prefix = CompanySetting::getSetting(
            'invoice_prefix',
            $request->header('company')
        );

        // Generate Hash
        $hash = General::generateUniqueId();
        while (Invoice::where('unique_hash', $hash)->count() > 0) {
            $hash = General::generateUniqueId();
        }

        $newInvoice = Invoice::create([
            'invoice_date' => $date->format('Y-m-d'),
            'due_date' => $date->format('Y-m-d'),
            'invoice_number' => $invoice_prefix."-".Invoice::getNextInvoiceNumber($invoice_prefix),
            'reference_number' => $invoice->reference_number,
            'user_id' => $invoice->user_id,
            'company_id' => $request->header('company'),
            'invoice_template_id' => 1,
            'status' => Invoice::STATUS_DRAFT,
            'paid_status' => Invoice::STATUS_UNPAID,
            'sub_total' => $invoice->sub_total,
            'discount' => $invoice->discount,
            'discount_type' => $invoice->discount_type,
            'discount_val' => $invoice->discount_val,
            'total' => $invoice->total,
            'due_amount' => $invoice->total,
            'tax_per_item' => $invoice->tax_per_item,
            'discount_per_item' => $invoice->discount_per_item,
            'tax' => $invoice->tax,
            'notes' => $invoice->notes,
            'unique_hash' => $hash,
        ]);

        $invoice->load('items.taxes');
        $invoiceItems = $invoice->items->toArray();

        foreach ($invoiceItems as $invoiceItem) {
            $invoiceItem['company_id'] = $request->header('company');
            $invoiceItem['name'] = $invoiceItem['name'];
            $item = $newInvoice->items()->create($invoiceItem);

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                foreach ($invoiceItem['taxes'] as $tax) {
                    $tax['company_id'] = $request->header('company');

                    if ($tax['amount']) {
                        $item->taxes()->create($tax);
                    }
                }
            }
        }

        if ($invoice->taxes) {
            foreach ($invoice->taxes->toArray() as $tax) {
                $tax['company_id'] = $request->header('company');
                $newInvoice->taxes()->create($tax);
            }
        }

        $newInvoice = Invoice::with([
            'items',
            'user',
            'invoiceTemplate',
            'taxes',
        ])->find($newInvoice->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'invoice' => $newInvoice,
            'success' => true,
        ], "message" => "CloneInvoice invoke"]];
        LogsDev::finishLog($log, $res, $time, 'D', "CloneInvoice invoke");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        // Logs por modulo
        LogsModule::createLog("Invoices", "Clone", "admin/invoices", $newInvoice->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Invoice Clone: ".$newInvoice->invoice_number);

        return response()->json([
            'invoice' => $newInvoice,
            'success' => true,
        ]);
    }
}
