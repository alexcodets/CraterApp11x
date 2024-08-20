<?php

namespace Crater\Http\Controllers\V1\Report;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Country;
use Crater\Models\Invoice;
use Crater\Models\InvoiceItem;
use Crater\Models\Item;
use Crater\Models\LogsDev;
use Crater\Models\PbxServices;
use Crater\Models\State;
use Crater\Models\Tax;
use Crater\Models\TaxAgency;
use Crater\Models\TaxCategory;
use Crater\Models\TaxType;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Log;
use PDF;
use Response;

class TaxSummaryReportController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, $hash)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $request->merge(['hash' => $hash]);
        $log = LogsDev::initLog($request, "", "D", "TaxSummaryReportController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company = Company::where('unique_hash', $hash)->first();

        $locale = CompanySetting::getSetting('language', $company->id);

        App::setLocale($locale);

        //se define el tipo de reporte

        $title = "BY GENERAL";
        $typereporte = "general";

        if ($request->has('type')) {
            if ($request->type == "customer") {
                $title = "BY CUSTOMER";
                $typereporte = "customer";
            }

            if ($request->type == "item") {
                $title = "BY ITEM";
                $typereporte = "item";
            }
        }

        //filtros
        $taxes = TaxType::where("company_id", $company->id);

        $enc = false;
        $includeCdr = false;
        $taxesCdr = [];
        $secondtitle = "";
        $dateFormat = CompanySetting::getSetting('carbon_date_format', $company->id);
        $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format($dateFormat);
        $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->format($dateFormat);

        if ($request->has('country')) {
            if ($request->country != "null") {
                $taxes = $taxes->where("country_id", $request->country);
                $country = Country::where("id", $request->country)->first();
                $secondtitle = $secondtitle." ".$country->name;
                $enc = true;
            }

        }

        if ($request->has('state')) {
            if ($request->state != "null") {
                $taxes = $taxes->where("state_id", $request->state);
                $state = State::where("id", $request->state)->first();
                $secondtitle = $secondtitle." | ".$state->name;
                $enc = true;
            }

        }

        if ($request->has('taxagency')) {
            if ($request->taxagency != "null") {
                $taxes = $taxes->where("tax_agency_id", $request->taxagency);
                $taxagency = TaxAgency::where("id", $request->taxagency)->first();
                $secondtitle = $secondtitle." | ".$taxagency->name;
                $enc = true;
            }

        }

        if ($request->has('taxcategory')) {
            if ($request->taxcategory != "null") {
                $taxes = $taxes->where("tax_category_id", $request->taxcategory);
                $category = TaxCategory::where("id", $request->taxcategory)->first();
                $secondtitle = $secondtitle." | ".$category->name;
                $enc = true;
            }

        }

        if ($request->has('city')) {
            if ($request->city != null && $request->city != "null") {
                $taxes = $taxes->where("city", "like", "%".$request->city."%");
                $enc = true;
            }

        }

        if ($request->has('county')) {
            if ($request->county != null && $request->county != "null") {
                $taxes = $taxes->where("county", "like", "%".$request->county."%");
                $enc = true;
            }
        }

        if ($request->has('include_cdr')) {

            $subtotalAmountCdr = 0;
            if ($request->include_cdr === 'true') {
                $taxesCdr = \DB::select(
                    'select sum(htt.amount) as amount, sum(htt.tax) as tax, tt.name
                    	from history_call_indi_tax_types as htt
                    	left join pbx_services_tax_types_cdr as stt on htt.pbx_services_id = stt.pbx_services_id 
                    	left join tax_types as tt on tt.id = stt.tax_types_id
                    	where htt.created_at between ? AND ?
                    	group by htt.taxable_type, tt.name
                    ',
                    [$request->from_date, $request->to_date]
                );

                $includeCdr = true;
                $enc = true;

                foreach ($taxesCdr as $tax) {
                    $subtotalAmountCdr += $tax->tax;
                }
            }
        }

        $arrayfilter = [];
        $arrayfilter = $request->only(['from_date', 'to_date']);
        $arrayfilter["customer"] = 0;
        $arrayfilter["item"] = 0;
        $arrayfilter["item_id"] = 0;

        $taxes = $taxes->get()->pluck('id')->toArray();

        $taxTypes = Tax::with('taxType', 'invoice', 'invoiceItem')
            ->whereCompany($company->id)
            ->whereInvoicesFilters($arrayfilter)
            ->where("tax_type_id", "!=", 0)
            ->taxAttributes()
            ->get();

        /// Se aplican los filtros
        $secondtitlefinal = "";
        $totalAmount = 0;
        $subtotalAmount = 0;

        if ($enc == true) {
            $taxTypes = Tax::with('taxType', 'invoice', 'invoiceItem')
                ->whereCompany($company->id)
                ->whereInvoicesFilters($arrayfilter)
                ->where("tax_type_id", "!=", 0)
                ->whereIN("tax_type_id", $taxes)
                ->taxAttributes()
                ->get();

            $secondtitlefinal = "Report for: ".$secondtitle;
        }

        foreach ($taxTypes as $taxType) {

            $totalAmount += $taxType->total_tax_amount;
        }

        if ($includeCdr) {
            $subtotalAmount = $totalAmount;
            $totalAmount = $subtotalAmount + ($subtotalAmountCdr * 100);
        }

        ///////////////////////////customer taxes reporte
        $arraycustomer = [];
        $cont = 0;
        if ($typereporte == "customer") {
            $users = User::where("role", "customer")->whereNull("deleted_at")->get();

            $totalAmount = 0;
            foreach ($users as $user) {
                $arrayfilter["customer"] = $user->id;
                $taxTypesbyuser = Tax::with('taxType', 'invoice', 'invoiceItem')
                    ->whereCompany($company->id)
                    ->whereInvoicesFilters($arrayfilter)
                    ->where("tax_type_id", "!=", 0)

                    ->taxAttributes()
                    ->get()->count();

                if ($taxTypesbyuser > 0) {

                    $taxTypesbyuser = Tax::with('taxType', 'invoice', 'invoiceItem')
                        ->whereCompany($company->id)
                        ->whereInvoicesFilters($arrayfilter)
                        ->where("tax_type_id", "!=", 0)
                        ->taxAttributes()
                        ->get();

                    if ($enc == true) {

                        $taxTypesbyuser = Tax::with('taxType', 'invoice', 'invoiceItem')
                            ->whereCompany($company->id)
                            ->whereInvoicesFilters($arrayfilter)
                            ->where("tax_type_id", "!=", 0)
                            ->whereIN("tax_type_id", $taxes)
                            ->taxAttributes()
                            ->get();

                        $secondtitlefinal = "Report for: ".$secondtitle;
                    }

                    if($taxTypesbyuser->isNotEmpty()) {

                        $subtotalAmountCdr = 0;
                        $taxCdrByUser = \DB::select(
                            'select tt.name, sum(htt.amount) as amount, sum(htt.tax) as tax, htt.taxable_id, htt.taxable_type
                            from history_call_indi_tax_types as htt
                            left join pbx_services_tax_types_cdr stt on htt.pbx_services_id = stt.pbx_services_id
                            left join tax_types tt on tt.id = stt.tax_types_id
                            left join pbx_services ps on htt.pbx_services_id = ps.id
                            where htt.created_at between ? AND ? AND ps.customer_id = ?
                            group by htt.taxable_id, htt.taxable_type',
                            [$request->from_date, $request->to_date, $user->id]
                        );

                        foreach ($taxCdrByUser as $tax) {
                            $subtotalAmountCdr += $tax->tax;
                        }

                        $arraycustomer[$cont]["taxesCdr"] = $taxCdrByUser;
                        $arraycustomer[$cont]["subtotalCdr"] = $subtotalAmountCdr;

                        //////////////////////////////////////////////////////////

                        $arraycustomer[$cont]["customer"] = $user;
                        $arraycustomer[$cont]["taxtype"] = $taxTypesbyuser;
                        $totalamountcust = 0;

                        foreach ($taxTypesbyuser as $taxType) {
                            $totalamountcust += $taxType->total_tax_amount;
                        }

                        $arraycustomer[$cont]["total"] = $totalamountcust;
                        $totalAmount += $totalamountcust + ($request->include_cdr === 'true' ? $subtotalAmountCdr * 100 : 0);
                        $cont++;

                    }
                }
            }
        }

        ///////////////////////////customer taxes item
        $arrayitems = [];
        $cont = 0;
        if ($typereporte == "item") {

            $totalAmount = 0;
            $items = Item::whereNull("deleted_at")->get();
            $start = Carbon::createFromFormat('Y-m-d', $request->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $request->get('to_date'));

            $invoicesarray = Invoice::whereNull("deleted_at")->where("status", "COMPLETED")->where("paid_status", "PAID")->where("tax_per_item", "YES")->whereBetween(
                'invoice_date',
                [$start->format('Y-m-d'), $end->format('Y-m-d')]
            )->pluck('id')->toArray();

            $arrayfilter["item"] = 1;
            foreach ($items as $item) {
                $contador = InvoiceItem::where("item_id", $item->id)->whereIN("invoice_id", $invoicesarray)->get()->count();
                if ($contador > 0) {
                    $contador = InvoiceItem::where("item_id", $item->id)->whereIN("invoice_id", $invoicesarray)->pluck('id')->toArray();
                    $ultrataxes = Tax::where("invoice_item_id", $contador)->get()->count();

                    if ($enc == true) {
                        $ultrataxes = Tax::where("invoice_item_id", $contador)->whereIN("tax_type_id", $taxes)->get()->count();
                    }
                    if ($ultrataxes > 0) {
                        $ultrataxes = Tax::selectRaw('sum(amount) as sum, tax_type_id')->where("invoice_item_id", $contador)->groupBy('tax_type_id')->get();

                        if ($enc == true) {
                            $ultrataxes = Tax::selectRaw('sum(amount) as sum, tax_type_id')->whereIN("tax_type_id", $taxes)->where("invoice_item_id", $contador)->groupBy('tax_type_id')->get();

                        }

                        $arrayitems[$cont]["item"] = $item;
                        $listax = [];
                        $cont2 = 0;
                        $totalitem = 0;
                        foreach ($ultrataxes as $ultratax) {

                            $taxtypei = TaxType::where("id", $ultratax->tax_type_id)->first();
                            $listax[$cont2]["name"] = $taxtypei->name;
                            $listax[$cont2]["amount"] = $ultratax->sum;
                            $totalitem = $totalitem + $ultratax->sum;
                            $cont2++;
                        }
                        $arrayitems[$cont]["taxes"] = $listax;
                        $arrayitems[$cont]["totalitem"] = $totalitem;

                        $totalAmount = $totalAmount + $totalitem;
                        $cont++;
                    }
                }

            }
        }

        $colors = [
            'primary_text_color',
            'heading_text_color',
            'section_heading_text_color',
            'border_color',
            'body_text_color',
            'footer_text_color',
            'footer_total_color',
            'footer_bg_color',
            'date_text_color',
        ];

        $colorSettings = CompanySetting::whereIn('option', $colors)
            ->whereCompany($company->id)
            ->get();

        $infoCompany = $this->getTextInformationCompanyReports($company);

        view()->share([
            'info_company' => $infoCompany,
            'taxTypes' => $taxTypes,
            'taxesCdr' => $taxesCdr,
            'includeCdr' => $includeCdr,
            'subtotalTaxAmount' => $subtotalAmount,
            'subtotalTaxAmountCdr' => $subtotalAmountCdr,
            'totalTaxAmount' => $totalAmount,
            'colorSettings' => $colorSettings,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'title' => $title,
            'reporte' => $secondtitlefinal,
            'type' => $typereporte,
            'arraycustomer' => $arraycustomer,
            'arrayitems' => $arrayitems,

        ]);

        $pdf = PDF::loadView('app.pdf.reports.tax-summary');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => true, "message" => "TaxSummaryReport Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxSummaryReport Payments");
        /////////////////////////////////////////

        if ($request->has('download')) {
            return $pdf->download();
        }

        return $pdf->stream();
    }

    public function exportCsv(Request $request)
    {
        \Log::debug("export csv");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "TaxSummaryReportController", "exportCsv");
        /////////////////////////////////////////

        $company = $request->user()->company;

        $taxesCompany = TaxType::where("company_id", $company->id)
        ->whereState($request->get('state_id'))
        ->whereCountry($request->get('country_id'))
        ->whereAgency($request->get('agency_id'))
        ->whereCategory($request->get('category_id'))
        ->whereCity($request->get('city'))
        ->whereCounty($request->get('county'))
        ->get()
        ->pluck('id')->toArray();

        //\Log::debug("taxes = tax_tpyes");
        //\Log::debug($taxesCompany);

        $from_date = Carbon::createFromFormat('Y-m-d', $request->get('from_date'));
        $to_date = Carbon::createFromFormat('Y-m-d', $request->get('to_date'));

        // all invoice
        $invoices = Invoice::where("company_id", $company->id)
        ->whereNull("deleted_at")
        ->where("status", "COMPLETED")
        ->where("paid_status", "PAID")
        ->with(['taxes', 'user:id,customcode', 'items.taxes'])
        ->invoicesBetween($from_date, $to_date)
        ->get();
        $invoicesFilter = collect($invoices)->filter(function ($invoice) use ($taxesCompany) {
            return collect($invoice->taxes)->whereIn('tax_type_id', $taxesCompany)->count() > 0 ||
            collect($invoice->items)->filter(function ($invoice) use ($taxesCompany) {
                return collect($invoice->taxes)->whereIn('tax_type_id', $taxesCompany)->count() > 0;
            })->count() > 0;
        });
        // all Pbx_services
        $services = PbxServices::where("company_id", $company->id)->with(['pbxTaxTypesHistory.taxable', 'user:id,customcode'])->get();
        // export $invoices to csv
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=taxes.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];
        $columns = [
            "Tax",
            "Amount"
        ];

        // filtrar por rangos de fechas
        $services = collect($services)->filter(function ($service) use ($from_date, $to_date) {
            return  $service->pbxTaxTypesHistory->filter(function ($history) use ($from_date, $to_date) {
                return $history->whereBetween('created_at', [$from_date, $to_date])->count() > 0;
            })->count() > 0;
        });
        $services_array = [];
        foreach ($services as $service) {
            $services_array[$service->id] = [
                'pbx_services_number' => $service->pbx_services_number,
                'customcode' => $service->user->customcode,
                'date_prev' => $service->date_prev,
                'renewal_date' => $service->renewal_date,
                'pbx_tax_types_history_unique' => []
            ];

            foreach ($service->pbxTaxTypesHistory as $taxHistory) {
                $findTaxeIndex = collect($services_array[$service->id]['pbx_tax_types_history_unique'])->search(function ($item) use ($taxHistory) {
                    return $item['taxable_id'] == $taxHistory['taxable_id'] && $item['taxable_type'] == $taxHistory['taxable_type'];
                });
                if(count($services_array[$service->id]['pbx_tax_types_history_unique']) > 0 && empty($findTaxeIndex)) {
                    // si existe suma el amount
                    $services_array[$service->id]['pbx_tax_types_history_unique'][$findTaxeIndex]['amount'] += $taxHistory->amount;
                } else {
                    // si no existe agregarlo
                    array_push($services_array[$service->id]['pbx_tax_types_history_unique'], [
                        'taxable_type' => $taxHistory->taxable_type,
                        'taxable_id' => $taxHistory->taxable_id,
                        'porcent' => $taxHistory->porcent,
                        'amount' => $taxHistory->amount,
                        'tax' => $taxHistory->tax,
                        'name' => $taxHistory->taxable->name
                    ]);
                }
            }
        }

        $callback = function () use ($invoicesFilter, $services_array, $taxesCompany, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // format invoices
            foreach($invoicesFilter as $invoice) {
                \Log::debug("invoice");
                \Log::debug($invoice);
                $taxes = $invoice->taxes;
                $tax_name = "";
                $tax_amount = 0;

                foreach($invoice->items as $item) {
                    foreach($item->taxes as $tax) {
                        $tax_name = $tax->name;
                        $tax_amount = $tax->amount / 100;
                        //$taxes_array[] = 'Tax: ' .$tax->name . " - " .'Amount: '. $tax->amount / 100;
                    }
                }
                /*
                foreach($taxes as $tax) {
                    $taxes_array[] = 'Tax: ' .$tax->name . " - " .'Amount: '. $tax->amount / 100;
                }
                $taxes_string = implode(", ", $taxes_array);
                */
                $row = [
                    $tax_name,
                    $tax_amount,
                ];
                fputcsv($file, $row);
            }

            // format services
            foreach($services_array as $service) {
                \Log::debug("services");
                $taxes = $service['pbx_tax_types_history_unique'];
                /*
                $taxes_array = array();
                $subTototal = 0;
                */
                $tax_name = "";
                $tax_amount = 0;
                foreach($taxes as $tax) {
                    $tax_name = $tax['name'];
                    $tax_amount = $tax['amount'];
                    /*
                    $taxes_array[] = 'Tax: ' .$tax['name'] . " - " .'Amount: '. number_format($tax['tax'], 5, '.', '');
                    $subTototal += $tax['amount'];
                    */
                }
                //$taxes_string = implode(", ", $taxes_array);

                $row = [
                    $tax_name,
                    $tax_amount,
                ];
                fputcsv($file, $row);
            }
            fclose($file);
        };

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => true, "message" => "TaxSummaryReport exportCsv Taxes"];
        LogsDev::finishLog($log, $res, $time, 'D', "TaxSummaryReport exportCsv Taxes");


        return Response::stream($callback, 200, $headers);
    }

    public function getTextInformationCompanyReports($company)
    {

        $text = CompanySetting::where('option', 'company_report_info')->where('company_id', $company->id)->get();

        $user = Auth::user();

        $user->load([
            'addresses',
            'addresses.country',
            'company',
            'company.address',
            'company.address.country',
        ]);

        $keys = [
            '{COMPANY_NAME}',
            '{COMPANY_NUMBER}',
            '{COMPANY_COUNTRY}',
            '{COMPANY_STATE}',
            '{COMPANY_CITY}',
            '{COMPANY_ADDRESS_STREET_1}',
            '{COMPANY_ADDRESS_STREET_2}',
            '{COMPANY_PHONE}',
            '{COMPANY_ZIP_CODE}',
            '{STATE_CODE}'
        ];

        $state = State::where('id', $user->company->address->state_id)->first();

        $values = [
            $company->name,
            $company->company_identifier,
            $user->company->address->country->name,
            $state ? $state->name : 'N/A' ,
            $user->company->address->city,
            $user->company->address->address_street_1,
            $user->company->address->address_street_2,
            $user->company->address->phone,
            $user->company->address->zip_code,
            $state ? $state->code : 'N/A',
        ];

        if(! $text->isEmpty()) {
            return $this->removeAttributesHtml(str_replace($keys, $values, $text->first()->value));
        }

        return '';

    }

    public function removeAttributesHtml($string)
    {
        $temp = str_replace('<p>', '', $string);
        $temp = str_replace('</p>', '', $temp);
        $temp = str_replace('<strong>', '', $temp);
        $temp = str_replace('</strong>', '', $temp);
        $temp = str_replace('<em>', '', $temp);
        $temp = str_replace('</em>', '', $temp);
        $temp = str_replace('<s>', '', $temp);
        $temp = str_replace('</s>', '', $temp);
        $temp = str_replace('<u>', '', $temp);
        $temp = str_replace('</u>', '', $temp);
        $temp = str_replace('<code>', '', $temp);
        $temp = str_replace('</code>', '', $temp);
        $temp = str_replace('<h1>', '', $temp);
        $temp = str_replace('</h1>', '', $temp);
        $temp = str_replace('<h2>', '', $temp);
        $temp = str_replace('</h2>', '', $temp);
        $temp = str_replace('<h3>', '', $temp);
        $temp = str_replace('</h3>', '', $temp);
        $temp = str_replace('<ul>', '', $temp);
        $temp = str_replace('</ul>', '', $temp);
        $temp = str_replace('<li>', '', $temp);
        $temp = str_replace('</li>', '', $temp);
        $temp = str_replace('<ol>', '', $temp);
        $temp = str_replace('</ol>', '', $temp);
        $temp = str_replace('<blockquote>', '', $temp);
        $temp = str_replace('</blockquote>', '', $temp);
        $temp = str_replace('<pre>', '', $temp);
        $temp = str_replace('</pre>', '', $temp);

        return $temp;
    }
}
