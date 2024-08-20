<?php

namespace Crater\Http\Controllers\V1\Estimate;

use Carbon\Carbon;
use Crater\Helpers\General;
use Crater\Http\Controllers\Controller;
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConvertEstimateController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Estimate $estimate
     * @return JsonResponse
     */
    public function __invoke(Request $request, Estimate $estimate): JsonResponse
    {
        try {
            //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
            $time = microtime(true);
            $request2 = $request;
            $request2->merge(['Estimate' => $estimate]);
            $log = LogsDev::initLog($request2, "", "D", "ConvertEstimateController", "__invoke");
            ///////////////////////////////////////

            $estimate->load(['items', 'items.taxes', 'user', 'estimateTemplate', 'taxes']);

            // Logs por modulo
            LogsModule::createLog("Estimates", "Update", "admin/estimates", $estimate->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Estimate to  Invoice: ".$estimate->estimate_number);

            $invoice_date = Carbon::now();
            $due_date = Carbon::now()->addDays(7);

            $invoice_prefix = CompanySetting::getSetting(
                'invoice_prefix',
                $request->header('company')
            );

            $invoice = Invoice::create([
                'creator_id' => Auth::id(),
                'invoice_date' => $invoice_date->format('Y-m-d'),
                'due_date' => $due_date->format('Y-m-d'),
                'invoice_number' => $invoice_prefix."-".Invoice::getNextInvoiceNumber($invoice_prefix),
                'reference_number' => $estimate->reference_number,
                'user_id' => $estimate->user_id,
                'company_id' => $request->header('company'),
                'invoice_template_id' => 1,
                'status' => Invoice::STATUS_DRAFT,
                'paid_status' => Invoice::STATUS_UNPAID,
                'sub_total' => $estimate->sub_total,
                'discount' => $estimate->discount,
                'discount_type' => $estimate->discount_type,
                'discount_val' => $estimate->discount_val,
                'total' => $estimate->total,
                'due_amount' => $estimate->total,
                'tax_per_item' => $estimate->tax_per_item,
                'discount_per_item' => $estimate->discount_per_item,
                'tax' => $estimate->tax,
                'notes' => $estimate->notes,
                'unique_hash' => General::generateUniqueId(),
            ]);

            if (Invoice::where('unique_hash', $invoice->id)->count() != 1) {
                $invoice->unique_hash = General::generateUniqueId();
                $invoice->save();
            }

            $invoiceItems = $estimate->items->toArray();

            foreach ($invoiceItems as $invoiceItem) {
                $invoiceItem['company_id'] = $request->header('company');
                $invoiceItem['name'] = $invoiceItem['name'];
                $item = $invoice->items()->create($invoiceItem);

                if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                    foreach ($invoiceItem['taxes'] as $tax) {
                        $tax['company_id'] = $request->header('company');

                        if ($tax['amount']) {
                            $item->taxes()->create($tax);
                        }
                    }
                }
            }

            if ($estimate->taxes) {
                foreach ($estimate->taxes->toArray() as $tax) {
                    $tax['company_id'] = $request->header('company');
                    unset($tax['estimate_id']);
                    $invoice->taxes()->create($tax);
                }
            }

            $invoice = Invoice::with([
                'items',
                'user',
                'invoiceTemplate',
                'taxes',
            ])->find($invoice->id);

            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => ["datamesage" => [
                'invoice' => $invoice,
            ], "message" => "__invoke"]];
            LogsDev::finishLog($log, $res, $time, 'D', "__invoke");
            /////////////////////////////////////////
        } catch (\Throwable $th) {
            \Log::debug($th);
        }

        return response()->json([
            'invoice' => $invoice,
        ]);
    }
}
