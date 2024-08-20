<?php

namespace Crater\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Crater\Traits\GeneratesPdfTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Log;

class HoldInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;
    use GeneratesPdfTrait;

    protected $fillable = [
        'invoice_date',
        'due_date',
        'user_id',
        'total',
        'due_amount',
        'sub_total',
        'tax',
        'discount_type',
        'discount',
        'discount_val',
        'tip_type',
        'tip',
        'tip_val',
        'cash_register_id',
        'notes',
        'company_id',
        'description',
        'store_id'
    ];

    public function holdContact(): HasMany
    {
        return $this->hasMany(HoldContact::class);
    }

    public function holdItems(): HasMany
    {
        return $this->hasMany(HoldItem::class);
    }

    public function holdTaxes(): HasMany
    {
        return $this->hasMany(HoldTax::class);
    }

    public function holdTables(): HasMany
    {
        return $this->hasMany(HoldTable::class);
    }

    public function getPdfData($request)
    {
        try {

            $taxTypes = [];
            $taxes = [];
            $labels = [];

            // if ($request->tax_per_item === 'YES') {
            //     foreach ($request->items as $item) {
            //         foreach ($item->taxes as $tax) {
            //             if (!in_array($tax->name, $taxTypes)) {
            //                 array_push($taxTypes, $tax->name);
            //                 array_push($labels, $tax->name . ' (' . $tax->percent . '%)');
            //             }
            //         }
            //     }

            //     foreach ($taxTypes as $taxType) {
            //         $total = 0;

            //         foreach ($request->items as $item) {
            //             foreach ($item->taxes as $tax) {
            //                 if ($tax->name == $taxType) {
            //                     $total += $tax->amount;
            //                 }
            //             }
            //         }

            //         array_push($taxes, $total);
            //     }
            // }

            $isPdfPos = $request->is_pdf_pos;
            $isInvoicePos = $request->is_invoice_pos;

            if ($isInvoicePos) {

                // $invoiceContact = DB::table('contact_invoice')->where('invoice_id', $request->id)->get();
                $invoiceContact = null;
                // $cashRegister = DB::table('cash_register_invoice')->select('*')->join('cash_register', 'cash_register.id', '=', 'cash_register_invoice.cash_register_id')
                //     ->where('cash_register_invoice.invoice_id', $request->id)->get();
                $cashRegister = DB::table('cash_register')->where('id', $request->cash_register_id)->first();
            } else {
                $cashRegister = null;
                $invoiceContact = null;
            }
            if ($isPdfPos) {

                $invoiceTemplate = InvoiceTemplate::where('view', 'template_pos')->first();
                $tablesSelected = DB::table('invoices_tables')->where('invoice_id', $request->id)->get();
            } else {
                $tablesSelected = [];
                $invoiceTemplate = InvoiceTemplate::find($request->invoice_template_id);
            }

            $company = User::find($request->user_id);

            $request['user'] = $company;
            // $logo = $company->logo_base_64;

            $request['company'] = auth()->user()->company->with('address');
            $colorInvoice = CompanySetting::getSetting('color_invoice', $request->company_id);
            $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
            $solaNOmultipe = PaymentMethod::where("is_multiple", 0)->pluck("id")->toarray();
            $listpay = Payment::where("invoice_id", $request->id)->whereNotNull("transaction_status")->whereIn("payment_method_id", $solaNOmultipe)->get();

            $payarray = [];
            $cont = 0;
            $totalpay = 0;

            /// foreach para pagos normales
            foreach ($listpay as $pay) {
                $payarray[$cont]["payment_date"] = $pay->payment_date;
                $payarray[$cont]["payment_number"] = $pay->payment_number;
                $payarray[$cont]["transaction_status"] = $pay->transaction_status;
                $payarray[$cont]["responsible"] = $pay->creator_id != null ? DB::table('users')->where('id', $pay->creator_id)->get() : null;
                $payarray[$cont]["amount"] = $pay->amount;
                $authorize = Payment::join('authorize', 'payments.authorize_id', 'authorize.id')->select('authorize.*')->where('payments.id', $pay->id)->first();
                $payarray[$cont]["type"] = null;
                if ($authorize != null) {

                    if ($authorize->ACH_type == null) {
                        $payarray[$cont]["type"] = "Credit Card";
                        $payarray[$cont]["card_number"] = $authorize->card_number;
                        $payarray[$cont]["credit_card"] = $authorize->credit_card;
                        $payarray[$cont]["transaction_id"] = $authorize->transaction_id;
                    } else {
                        $payarray[$cont]["type"] = "ACH";
                        $payarray[$cont]["type_ach"] = $authorize->ACH_type;
                        $payarray[$cont]["account_number"] = substr($authorize->account_number, -4);
                        $payarray[$cont]["bank_name"] = $authorize->bank_name;
                        $payarray[$cont]["num_check"] = $authorize->num_check;
                        $payarray[$cont]["routing_number"] = $authorize->routing_number;
                        $payarray[$cont]["transaction_id"] = $authorize->transaction_id;
                    }
                }

                if ($pay->payment_method_id == null) {
                    $payarray[$cont]["payment_method"] = "Customer Credit Balance";
                } else {
                    $paymentmethod = PaymentMethod::where("id", $pay->payment_method_id)->first();
                    if ($paymentmethod != null) {
                        $payarray[$cont]["payment_method"] = $paymentmethod->name;
                    } else {
                        $payarray[$cont]["payment_method"] = "N/A";
                    }
                }

                if ($pay["transaction_status"] == "Approved") {
                    $totalpay = $totalpay + $payarray[$cont]["amount"];
                }

                $cont++;
            }

            $solaNOmultipe = PaymentMethod::where("is_multiple", 1)->pluck("id")->toarray();
            $listpay = Payment::where("invoice_id", $request->id)->whereNotNull("transaction_status")->whereIn("payment_method_id", $solaNOmultipe)->get();

            ///
            $received = null;
            $returned = null;
            /// foreach para pagos nque son multiple
            foreach ($listpay as $pay) {
                $listado = \DB::table('payments_payment_methods')->where('payment_id', $pay->id)->get();

                if ($listado->isNotEmpty()) {
                    $received += \DB::table('payments_payment_methods')
                        ->where('payment_id', $pay->id)
                        ->sum('received');

                    $returned += \DB::table('payments_payment_methods')
                        ->where('payment_id', $pay->id)
                        ->sum('returned');
                }

                foreach ($listado as $pay2) {
                    $payarray[$cont]["payment_date"] = $pay->payment_date;
                    $payarray[$cont]["payment_number"] = $pay->payment_number;
                    $payarray[$cont]["transaction_status"] = $pay->transaction_status;
                    $payarray[$cont]["amount"] = $pay2->amount;
                    $payarray[$cont]["type"] = null;
                    $paymentmethod = PaymentMethod::where("id", $pay2->payment_method_id)->first();
                    $payarray[$cont]["responsible"] = $pay->creator_id != null ? DB::table('users')->where('id', $pay->creator_id)->get() : null;
                    if ($paymentmethod != null) {
                        $payarray[$cont]["payment_method"] = $paymentmethod->name;
                    } else {
                        $payarray[$cont]["payment_method"] = "N/A";
                    }

                    $cont++;
                }

                if ($pay["transaction_status"] == "Approved") {
                    $totalpay = $totalpay + $pay->amount;
                }
            }

            // $query = $request->avalaraInvoiceCurrent;

            // $cont = 0;
            // $avalaraTaxesDeFinito = array();
            // $index = 0;
            // if ($query != null) {
            //     $query_2 = AvalaraTax::where('avalara_invoice_id', '=', $query->id)
            //         ->selectRaw('name, lvl, sum(amount) as total, item_id')
            //         ->groupBy('avalara_taxes.name', 'avalara_taxes.lvl')
            //         ->get();

            //     foreach ($query_2 as $q2) {
            //         $avalaraTaxesDeFinito[$cont]["name"] = $q2->name;
            //         $avalaraTaxesDeFinito[$cont]["lvl"] = $q2->lvl;
            //         $avalaraTaxesDeFinito[$cont]["total"] = $q2->total;
            //         //
            //         $item_ids = AvalaraTax::where('name', '=', $q2->name)->where('lvl', '=', $q2->lvl)->where('avalara_invoice_id', '=', $query->id)->get()->pluck('item_id');
            //         $avalaraTaxesDeFinito[$cont]["items"] = Item::whereIn('id', $item_ids)->get()->toArray();
            //         $cont++;
            //     }

            //     foreach ($avalaraTaxesDeFinito as $array) {
            //         if ($array["lvl"] == 0) {
            //             $avalaraTaxesDeFinito[$index]["lvl"] = "Federal";
            //         }
            //         if ($array["lvl"] == 1) {
            //             $avalaraTaxesDeFinito[$index]["lvl"] = "State";
            //         }
            //         if ($array["lvl"] == 2) {
            //             $avalaraTaxesDeFinito[$index]["lvl"] = "Country";
            //         }
            //         if ($array["lvl"] == 3) {
            //             $avalaraTaxesDeFinito[$index]["lvl"] = "City";
            //         }
            //         if ($array["lvl"] == 4) {
            //             $avalaraTaxesDeFinito[$index]["lvl"] = "Unincorporated";
            //         }
            //         $index++;
            //     }
            // }

            // $arrayitm = array();
            // $cont = 0;

            // if ($request->pbx_service_id != null) {
            //     $service = PbxServices::where("id", $request->pbx_service_id)->first();

            //     if ($service != null) {

            //         $pbxpac = PbxPackages::where("id", $service->pbx_package_id)->first();

            //         if ($pbxpac != null) {
            //             $listgroup = ProfileDidCustomDidGroups::where("profile_did_id", $pbxpac->template_did_id)->whereNULL("deleted_at")->pluck('custom_did_group_id');

            //             foreach ($listgroup as $tp) {

            //                 $customname = CustomDidGroups::where("id", $tp)->first();
            //                 $pop = TollFreeCustomDidGroup::where("custom_did_group_id", $tp)->pluck('toll_free_did_id');

            //                 if ($customname != null && count($pop) != 0) {
            //                     $arrayitm[$cont]["name"] = $customname->name;
            //                     $arrayitm[$cont]["list"] = $pop;
            //                     $cont++;
            //                 }
            //             }
            //         }
            //     }
            // }

            // late_fees array

            // $late_fees = InvoiceLateFee::where("invoice_id", $request->id)->get();
            // $total_fees = 0;

            // foreach ($late_fees as $lf) {
            //     $total_fees += $lf->total;
            // }

            // //
            // $servicecnumber = null;
            // if ($request->customer_packages_id != null) {
            //     $serviceC = CustomerPackage::where("id", $request->customer_packages_id)->first();
            //     if ($serviceC != null) {
            //         $servicecnumber = $serviceC->code;
            //     }
            // }

            // $pbx_service_detail = PbxServicesDetail::where("invoice_id", "=", $request->id)->count();

            // if ($pbx_service_detail > 0) {
            //     $pbx_serv_detail_count_extension = PbxServicesDetail::where("invoice_id", "=", $request->id)->first('count_extension');
            //     $pbx_serv_detail_count_did = PbxServicesDetail::where("invoice_id", "=", $request->id)->sum('count_did');

            //     $request->count_extensions = $pbx_serv_detail_count_extension;
            //     $request->count_extensions = $pbx_serv_detail_count_did;
            // } else {
            //     $invoice_count_extension = Invoice::where("id", "=", $request->id)->first('count_extension');
            //     $invoice_count_did = Invoice::where("id", "=", $request->id)->first('count_did');

            //     $request->count_extensions = $invoice_count_extension;
            //     $request->count_extensions = $invoice_count_did;
            // }

            // invoice_pbx_extension_detail (isEdited == 1 "Extensions")
            // $invoice_pbx_extension_detail = DB::table('invoice_pbx_extension_detail')
            //     ->where('invoice_id', $request->id)
            //     ->where('deleted_at', null)
            //     ->get();

            // $total_invoice_pbx_extension_detail = 0.00;
            // if ($invoice_pbx_extension_detail->isNotEmpty()) {
            //     foreach ($invoice_pbx_extension_detail as $extension) {
            //         $total_invoice_pbx_extension_detail += $extension->total;
            //     }
            // }

            // Dids - (isEdited == 0) -> invoice_pbx_did_detail
            // $invoice_dids = self::findInvoiceDids($request->id);

            // Dids - (isEdited == 1) -> invoice_pbx_did_detail
            // $invoice_pbx_did_detail = DB::table('invoice_pbx_did_detail')
            //     ->where('invoice_id', $request->id)
            //     ->where('deleted_at', null)
            //     ->get();

            // $total_invoice_pbx_did_detail = 0.00;
            // if ($invoice_pbx_did_detail->isNotEmpty()) {
            //     foreach ($invoice_pbx_did_detail as $did) {
            //         $total_invoice_pbx_did_detail += $did->total;
            //     }
            // }
            $data = [
                'tables_selected' => $tablesSelected,
                'cash_register' => $cashRegister,
                'invoice_contact' => $invoiceContact,
                'invoice' => $request->all(),
                // 'user_creator' => User::where('id', $request->creator_id)->first(),
                // 'company_address' => $request->getCompanyAddress(),
                // 'shipping_address' => $request->getCustomerShippingAddress(),
                // 'billing_address' => $request->getCustomerBillingAddress(),
                'user_creator' => auth()->user()->first(),
                'company_address' => [],
                'shipping_address' => [],
                'billing_address' => [],
                // 'notes' => $request->getNotes(),
                // 'logo' => $logo ?? null,
                'labels' => $labels,
                'taxes' => $taxes,
                // 'user' => User::find($request->user_id),
                'colorInvoice' => $colorInvoice,
                // 'Footer' => $request->getFooter(),
                'PaymentsArray' => $payarray,
                // received and returned
                'received' => $received,
                'returned' => $returned,
                //
                'PaymentsTotal' => $totalpay,
                // 'lateFees' => $late_fees,
                // 'totalFees' => $total_fees,
                // 'listcustomdid' => $arrayitm,
                // 'servicecnumber' => $servicecnumber,
                // Extensions (invoice -> isEdited == 1)
                // 'invoice_pbx_extension_detail' => $invoice_pbx_extension_detail,
                // 'total_invoice_pbx_extension_detail' => $total_invoice_pbx_extension_detail,
                // Dids (invoice -> isEdited == 1)
                // 'dids_group' => $invoice_dids,
                // 'invoice_pbx_did_detail' => $invoice_pbx_did_detail,
                // 'total_invoice_pbx_did_detail' => $total_invoice_pbx_did_detail,
                //
                // 'avalaraTaxesDeFinito' => $avalaraTaxesDeFinito,
                //"items_with_avalara_taxes" => $items_with_avalara_taxes
            ];
            view()->share($data);
            $pdf = PDF::loadView('app.pdf.invoice.template_hold_invoice');

            // if ($isPdfPos) {
            //     return $pdf->setPaper([0, 0, 200, 600], 'portrait');
            // } else {

            return $pdf;
        } catch (\Throwable $th) {
            Log::debug('error hold model 382', ['error' => $th]);
        }
    }
}
