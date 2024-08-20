<?php

namespace Crater\Http\Controllers\V1\Report;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Country;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\Payment;
use Crater\Models\PaymentMethod;
use Crater\Models\State;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use PDF;

class ProfitLossReportController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "ProfitLossReportController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $company = Company::where('unique_hash', $hash)->first();

        $locale = CompanySetting::getSetting('language', $company->id);

        //$excludexpm = PaymentMethod::where("generate_expense", 1)->pluck('id')->toarray();

        $excludeexp = Expense::whereNotNull("payment_id")->pluck('payment_id')->toarray();

        App::setLocale($locale);

        $showbalancetodebit = $request->get('showdebit');

        if ($showbalancetodebit == null || $showbalancetodebit == "NO") {
            $showbalancetodebit = "NO";
        } else {
            $showbalancetodebit = "YES";
        }

        ///list customers
        $listucosmter = null;
        $balancenocobrado = 0;
        if ($request->get('customer')) {
            $listucosmter = "";
            $list = explode(',', $request->get('customer'));
            $listname = User::whereIN("id", $list)->pluck("name")->toarray();
            foreach ($listname as $lname) {
                $listucosmter = $listucosmter." ".$lname." | ";
            }
            $balancenocobrado = User::whereIN("id", $list)->sum("balance");
        } else {
            $balancenocobrado = User::all()->sum("balance");
        }

        $countryname = null;
        if ($request->get('country')) {
            $countryob = Country::where("id", $request->get('country'))->first();
            if ($countryob != null) {
                $countryname = $countryob->name;
            }
        }

        $statename = null;
        if ($request->get('state')) {
            $countryob = State::where("id", $request->get('state'))->first();
            if ($countryob != null) {
                $statename = $countryob->name;
            }
        }

        $invoicesAmount = 0;
        $invoicesAmountpart = 0;
        $invoicesDueAmount = 0;
        $totalSales = 0;
        $totalReceipts = 0;
        $expenseCategories = null;
        $totalAmount = 0;
        $pmlist = [];
        $customername = null;
        $customernumber = null;

        $invoicesAmount = Invoice::whereCompany($company->id)
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
            ->wherePaidStatus(Invoice::STATUS_PAID)
            ->sum('total');

        $invoicesAmountpart = Invoice::whereCompany($company->id)
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
            ->wherePaidStatus(Invoice::STATUS_PARTIALLY_PAID)
            ->sum('total');

        $invoicesAmountpart = $invoicesAmountpart - Invoice::whereCompany($company->id)
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
            ->wherePaidStatus(Invoice::STATUS_PARTIALLY_PAID)
            ->sum('due_amount');

        $invoicesDueAmount = Invoice::whereCompany($company->id)
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
            ->where("status", "!=", "DRAFT")
            ->where("status", "!=", "SAVE_DRAFT")
            ->where("status", "!=", "COMPLETED")
            ->sum('due_amount');

        $totalSales = Invoice::whereCompany($company->id)
                              ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
                              ->where("status", "!=", "DRAFT")
                              ->where("status", "!=", "SAVE_DRAFT")
                              ->whereNull("deleted_at")
                              ->sum("total");

        $totalReceipts = Payment::whereCompany($company->id)
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))->where(function ($query) {
                $query->where('transaction_status', '=', "Approved")
                    ->orWhere('transaction_status', '=', "Unapply");
            })
            ->whereNotNull('invoice_id')
            ->whereNotNull('payment_method_id')
            ->whereNotIn('id', $excludeexp)
            ->where("applied_credit_customer", 0)
            ->sum('amount');

        ///payments para credito
        $totalReceiptsCredit = 0;
        if ($showbalancetodebit == "YES") {
            $totalReceiptsCredit = Payment::whereCompany($company->id)
                ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))->where(function ($query) {
                    $query->where('transaction_status', '=', "Approved")
                        ->orWhere('transaction_status', '=', "Approved");
                })
                ->whereNotIn('id', $excludeexp)
                ->whereNull("payment_method_id")
                ->whereNotNull('invoice_id')
                ->sum('amount');
        }

        $expenseCategories = Expense::with('category')
            ->whereCompany($company->id)
            ->where("status", "Active")
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
            ->expensesAttributes()
            ->get();

        $totalAmount = 0;
        foreach ($expenseCategories as $category) {
            $totalAmount += $category->total_amount;
        }

        $dateFormat = CompanySetting::getSetting('carbon_date_format', $company->id);
        $from_date = Carbon::createFromFormat('Y-m-d', $request->from_date)->format($dateFormat);
        $to_date = Carbon::createFromFormat('Y-m-d', $request->to_date)->format($dateFormat);

        //payment methods
        $pmarray = Payment::whereCompany($company->id)
            ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))->where(function ($query) {
                $query->where('transaction_status', '=', "Approved")
                ->orWhere('transaction_status', '=', "Unapply");
            })
            ->whereNotNull('payment_method_id')
            ->whereNotIn('id', $excludeexp)
            ->where("applied_credit_customer", 0)
            ->pluck('payment_method_id')->toarray();

        $pmarray = array_unique($pmarray);
        $cont = 0;
        $pmlist = [];
        $pmlist_ids = [];
        $totalpaymentmethods = 0;
        $exists_multiple_method = false;

        foreach ($pmarray as $pm) {
            if ($pm != null) {
                $paymentmethod = PaymentMethod::where("id", $pm)->first();

                if($paymentmethod->name != "Multiple") {
                    $Namemethod = "Custom Method";
                    if ($paymentmethod != null) {
                        $Namemethod = $paymentmethod->name;
                        if ($paymentmethod->generate_expense == 1) {
                            $Namemethod = $Namemethod."*";
                        }
                    }

                    $pmlist[$cont]["id"] = $paymentmethod->id;
                    $pmlist[$cont]["name"] = $Namemethod;

                    $pmlist[$cont]["amount"] = Payment::whereCompany($company->id)
                        ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
                        ->where(function ($query) {
                            $query->where('transaction_status', '=', "Approved")
                                ->orWhere('transaction_status', '=', "Unapply");
                        })
                       // ->whereNotNull('invoice_id')
                        ->whereNotNull('payment_method_id')
                        ->whereNotIn('id', $excludeexp)
                        ->where("applied_credit_customer", 0)
                        ->where("payment_method_id",  $pm)
                        ->sum('amount');


                    array_push($pmlist_ids, $paymentmethod->id);
                    $totalpaymentmethods += $pmlist[$cont]["amount"];
                    $cont++;
                } else {
                    $exists_multiple_method = true;
                    $multiple_payments_id = Payment::whereCompany($company->id)
                        ->applyFilters($request->only(['from_date', 'to_date', 'customer', 'country', 'state']))
                        ->where(function ($query) {
                            $query->where('transaction_status', '=', "Approved")
                                ->orWhere('transaction_status', '=', "Unapply");
                        })
                        //->whereNotNull('invoice_id')
                        ->whereNotNull('payment_method_id')
                        ->whereNotIn('id', $excludeexp)
                        ->where("applied_credit_customer", 0)
                        ->where("payment_method_id",  $pm)
                        ->pluck('id');
                }
            }
        }

        if($exists_multiple_method) {
            $pm_list_multiple = [];
            $payments_payment_methods_GROUP_BY = \DB::table('payments_payment_methods')
                   ->groupBy('payment_method_id')
                   ->whereIn('payment_id', $multiple_payments_id)
                   ->select(["payment_method_id as id", \DB::raw("SUM(amount) as amount")])->get();

            $totalpaymentmethods_multiple = 0;
            foreach($payments_payment_methods_GROUP_BY as $ppm_GROUP) {
                if(count($pmlist) > 0) {
                    if (in_array($ppm_GROUP->id, $pmlist_ids, true)) {
                        $key = array_search($ppm_GROUP->id, array_column($pmlist, 'id'));

                        $payment_method = [];
                        $payment_method["id"] = $pmlist[$key]["id"];
                        $payment_method["name"] = $pmlist[$key]["name"];
                        $amount = 0;
                        $amount += $ppm_GROUP->amount + $pmlist[$key]["amount"];
                        $payment_method["amount"] = $amount;

                        $totalpaymentmethods_multiple += $payment_method["amount"];
                        array_push($pm_list_multiple, $payment_method);
                    } else {
                        $paymentmethod = PaymentMethod::where("id", $ppm_GROUP->id)->first();

                        $payment_method = [];
                        $payment_method["id"] = $paymentmethod->id;
                        $payment_method["name"] = $paymentmethod->name;
                        $payment_method["amount"] = $ppm_GROUP->amount;

                        $totalpaymentmethods_multiple += $payment_method["amount"];
                        array_push($pm_list_multiple, $payment_method);
                    }
                } else {
                    $paymentmethod = PaymentMethod::where("id", $ppm_GROUP->id)->first();

                    $payment_method = [];
                    $payment_method["id"] = $paymentmethod->id;
                    $payment_method["name"] = $paymentmethod->name;
                    $payment_method["amount"] = $ppm_GROUP->amount;

                    $totalpaymentmethods_multiple += $payment_method["amount"];
                    array_push($pm_list_multiple, $payment_method);
                }
            }
        }

        if(count($pmlist) > 0 && $exists_multiple_method) {
            $pmlist = $pm_list_multiple;
            $totalpaymentmethods = $totalpaymentmethods_multiple;
        } elseif (count($pmlist) > 0 && ! $exists_multiple_method) {
            $pmlist = $pmlist;
        } elseif (count($pmlist) == 0 && $exists_multiple_method) {
            $pmlist = $pm_list_multiple;
            $totalpaymentmethods = $totalpaymentmethods_multiple;
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
            'company' => $company,
            'total_sales' => $totalSales,
            'income' => $totalReceipts,
            'expenseCategories' => $expenseCategories,
            'totalExpense' => $totalAmount,
            'colorSettings' => $colorSettings,
            'company' => $company,
            'from_date' => $from_date,
            'to_date' => $to_date,
            'total_payment' => $totalReceipts,

            'full_paid' => $invoicesAmount,
            'part_paid' => $invoicesAmountpart,
            'pending_balance' => $invoicesDueAmount,
            'credit' => $totalReceiptsCredit,
            'paymentmethodlist' => $pmlist,

            'totalpaymentmethods' => $totalpaymentmethods,

            'customername' => $customername,
            'customernumber' => $customernumber,
            'showbalancetodebit' => $showbalancetodebit,
            'listucosmter' => $listucosmter,
            'countryname' => $countryname,
            'statename' => $statename,
            'balancenocobrado' => $balancenocobrado,
        ]);

        $pdf = PDF::loadView('app.pdf.reports.profit-loss');

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => true, "message" => "ProfitLossReport Payments"];
        LogsDev::finishLog($log, $res, $time, 'D', "ProfitLossReport   Payments");
        /////////////////////////////////////////

        if ($request->has('download')) {
            return $pdf->download();
        }

        return $pdf->stream();
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
