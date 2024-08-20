<?php

namespace Crater\Http\Controllers\V1\CorePOS\Report;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Crater\CorePos\Models\PosCashRegister;
use Crater\Http\Controllers\Controller;
use Crater\Models\CashHistory;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Log;

class CashRegisterReportController extends Controller
{
    public function __invoke(Request $request,  $id, $hash)
    {
        try {
            $filter = $request->only([
                'cash_history_id'
            ]);

            $company = Company::where('unique_hash', $hash)->first();
            $cashRegister = PosCashRegister::select('cash_register.*', 'users.name as user_name')
                ->join('cash_register_cash_histories', 'cash_register_cash_histories.cash_register_id', '=', 'cash_register.id')
                ->join('users', 'cash_register_cash_histories.creator_id', '=', 'users.id')
                ->where('cash_register.id', $id)
                ->first();


            $company = Company::where('unique_hash', $hash)->first();
            $locale = CompanySetting::getSetting('language',  $company->id);
            App::setLocale($locale);
            $logo = $company->logo_base_64;

            $currencyId = CompanySetting::where('company_id', $company->id)->where('option', 'currency')->pluck('value');
            $currency = Currency::where('id', $currencyId)->pluck('name');
            $filter = collect($filter);

            if ($cashRegister) {



                $resultCashHistories = CashHistory::where('cash_register_id', $cashRegister->id)->latest()->get();
                if (! $filter->isEmpty()) {
                    $cashHistory = CashHistory::where('id', $filter->get('cash_history_id'))->first();
                } else {
                    $cashHistory = $resultCashHistories->first();
                }



                if ($cashHistory->close_date != null) {
                    $closeDate = $cashHistory->close_date;
                } else {
                    $closeDate = Carbon::now();
                }

                $cashRegisterInvoice = DB::table('cash_register_invoice')
                    ->select('payments.payment_method_id as method_id', DB::raw('SUM(payments.amount) as total_amount'), 'payment_methods.only_cash as only_cash', 'payment_methods.name as method_name')
                    ->join('payments', 'cash_register_invoice.invoice_id', '=', 'payments.invoice_id')
                    ->join('payments_payment_methods', 'payments.id', '=', 'payments_payment_methods.payment_id')
                    ->join('payment_methods', 'payments_payment_methods.payment_method_id', '=', 'payment_methods.id')
                    ->whereBetween('payments.created_at', [$cashHistory->open_date, $closeDate])
                    ->groupBy('payments_payment_methods.payment_method_id')
                    ->get();


                $invoicesAmount = DB::table('cash_register_invoice')
                    ->select(DB::raw('SUM(payments_payment_methods.received) as received'), DB::raw('SUM(payments_payment_methods.returned) as returned'))
                    ->join('invoices', 'cash_register_invoice.invoice_id', '=', 'invoices.id')
                    ->join('payments',  'payments.invoice_id', '=', 'invoices.id')
                    ->join('payments_payment_methods', 'payments.id', '=', 'payments_payment_methods.payment_id')
                    ->join('payment_methods', 'payments_payment_methods.payment_method_id', '=', 'payment_methods.id')
                    ->where('cash_register_invoice.cash_register_id', $cashRegister->id)
                    ->where('invoices.paid_status', 'PAID')
                    ->where('payment_methods.only_cash', 1)
                    ->whereBetween('invoices.created_at', [$cashHistory->open_date, $closeDate])
                    ->get();

                $amountIncome = DB::table('cash_register_cash_histories')
                    ->where('cash_register_id', $cashRegister->id)
                    ->where('type', 'I')
                    ->whereBetween('created_at', [$cashHistory->open_date, $closeDate])
                    ->sum('amount');

                $amountWithdrawal = DB::table('cash_register_cash_histories')
                    ->where('cash_register_id', $cashRegister->id)
                    ->where('type', 'R')
                    ->whereBetween('created_at', [$cashHistory->open_date, $closeDate])
                    ->sum('amount');

                $data = [
                    'company' => $company,
                    'logo' => $logo ?? null,
                    'cash_register' => $cashRegister,
                    'cash_history' => $cashHistory,
                    'currency' => $currency,
                    'detail_sales' => $cashRegisterInvoice,
                    'cash_income' => $amountIncome,
                    'cash_withdrawal' => $amountWithdrawal,
                    'invoices_amount' => $invoicesAmount->first(),
                    'last_amount' => $resultCashHistories->count() > 1 ? $resultCashHistories[1] : 0
                ];
            } else {

                $cashRegister = PosCashRegister::select('cash_register.*')
                    ->where('cash_register.id', $id)
                    ->first();

                $data = [
                    'company' => $company,
                    'logo' => $logo ?? null,
                    'cash_register' => $cashRegister,
                    'cash_history' => null,
                    'currency' => $currency,
                    'detail_sales' => null,
                    'cash_income' => 0,
                    'cash_withdrawal' => 0,
                    'invoices_amount' => null,
                    'last_amount' => null
                ];
            }

            view()->share($data);

            $pdf = PDF::loadView('app.pdf.reports.cashRegister');
            $reportPdf = $pdf->setPaper([0, 0, 200, 600], 'portrait');

            return $reportPdf->stream();
        } catch (\Throwable $th) {
            //throw $th;
            Log::debug('error ', ['error' => $th]);
        }
    }
}
