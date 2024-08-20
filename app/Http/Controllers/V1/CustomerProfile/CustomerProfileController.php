<?php

namespace Crater\Http\Controllers\V1\CustomerProfile;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerPackage;
use Crater\Models\CustomerTicket;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;
use Throwable;

class CustomerProfileController extends Controller
{
    public function mainInformation(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "mainInformation");

        $customer = Auth::user();

        $i = 0;
        $months = [];
        $invoiceTotals = [];
        $expenseTotals = [];
        $receiptTotals = [];
        $netProfits = [];
        $monthCounter = 0;
        $fiscalYear = CompanySetting::getSetting('fiscal_year', $customer->company_id);
        $startDate = Carbon::now();
        $start = Carbon::now();
        $end = Carbon::now();
        $terms = explode('-', $fiscalYear);

        if ($terms[0] <= $start->month) {
            $startDate->month($terms[0])->startOfMonth();
            $start->month($terms[0])->startOfMonth();
            $end->month($terms[0])->endOfMonth();
        } else {
            $startDate->subYear()->month($terms[0])->startOfMonth();
            $start->subYear()->month($terms[0])->startOfMonth();
            $end->subYear()->month($terms[0])->endOfMonth();
        }

        if ($request->has('previous_year')) {
            $startDate->subYear()->startOfMonth();
            $start->subYear()->startOfMonth();
            $end->subYear()->endOfMonth();
        }

        while ($monthCounter < 12) {
            array_push(
                $invoiceTotals,
                Invoice::whereBetween(
                    'invoice_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                    ->whereCompany($customer->company_id)
                    ->whereCustomer($customer->id)
                    ->whereNull('deleted_at')
                    ->sum('total') ?? 0
            );
            array_push(
                $expenseTotals,
                Expense::whereBetween(
                    'expense_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                    ->whereCompany($customer->company_id)
                    ->whereUser($customer->id)
                    ->sum('amount') ?? 0
            );
            array_push(
                $receiptTotals,
                Payment::whereBetween(
                    'payment_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                    ->whereCompany($customer->company_id)
                    ->whereCustomer($customer->id)
                    ->sum('amount') ?? 0
            );
            array_push(
                $netProfits,
                ($receiptTotals[$i] - $expenseTotals[$i])
            );
            $i++;
            array_push($months, $start->format('M'));
            $monthCounter++;
            $end->startOfMonth();
            $start->addMonth()->startOfMonth();
            $end->addMonth()->endOfMonth();
        }

        $start->subMonth()->endOfMonth();

        $salesTotal = Invoice::whereCompany($customer->company_id)
            ->whereBetween(
                'invoice_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->whereNull('deleted_at')
            ->whereCustomer($customer->id)
            ->sum('total');

        $totalReceipts = Payment::whereCompany($customer->company_id)
            ->whereBetween(
                'payment_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->whereCustomer($customer->id)
            ->sum('amount');

        $totalExpenses = Expense::whereCompany($customer->company_id)
            ->whereBetween(
                'expense_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->whereUser($customer->id)
            ->sum('amount');

        $netProfit = (int) $totalReceipts - (int) $totalExpenses;

        $balanceTotal = $customer->balance;

        $chartData = [
            'months' => $months,
            'invoiceTotals' => $invoiceTotals,
            'expenseTotals' => $expenseTotals,
            'receiptTotals' => $receiptTotals,
            'netProfit' => $netProfit,
            'netProfits' => $netProfits,
            'salesTotal' => $salesTotal,
            'totalReceipts' => $totalReceipts,
            'totalExpenses' => $totalExpenses,
            'balanceTotal' => $balanceTotal,
        ];

        $customer = User::with([
            'billingAddress',
            'shippingAddress',
            'billingAddress.country',
            'billingAddress.state',
            'shippingAddress.country',
            'currency',
            'fields.customField',
            'notes',
        ])->find($customer->id);
        // STATS
        $countServices = CustomerPackage::where('company_id', $customer->company_id)
            ->where('customer_id', $customer->id)
            ->where('status', 'A')
            ->count();

        $countPbxServices = PbxServices::where('company_id', $customer->company_id)
            ->where('customer_id', $customer->id)
            ->where('status', 'A')
            ->count();

        $invoicesForStats = Invoice::where('company_id', $customer->company_id)
            ->where('user_id', $customer->id)
            ->whereNotIn('status', ['DRAFT', 'SAVE_DRAFT', 'COMPLETED'])
            ->get();

        $countInvoices = $invoicesForStats->count();

        $totalAmountDue = $invoicesForStats->sum('due_amount');

        $maxAmountDue = $invoicesForStats->max('due_amount');

        $invoiceWithMaxDebit = $invoicesForStats->where('due_amount', '>=', $maxAmountDue)->first();

        $countEstimates = Estimate::whereCompany($customer->company_id)
            ->where('user_id', $customer->id)
            ->whereNotIn('status', ['DRAFT', 'REJECTED', 'EXPIRED'])
            ->count();

        $countTickets = CustomerTicket::where('company_id', $customer->company_id)
            ->where('user_id', $customer->id)
            ->where('status', '<>', 'C')
            ->count();

        $countPayments = Payment::where('company_id', $customer->company_id)
                                ->where('user_id', $customer->id)
                                ->whereNull('deleted_at')
                                ->count();

        $callRegisterTotalAmount = 0;

        if ($customer->status_payment == "prepaid") {
            $pbx_service_ids = PbxServices::where('company_id', $customer->company_id)
                ->where('customer_id', $customer->id)
                ->where('status', 'A')
                ->pluck('id')
                ->toArray();

            $callRegisterTotalAmount = CallDetailRegisterTotal::whereIn('pbx_services_id', $pbx_service_ids)
                ->whereColumn('exclusive_cost', '>', 'exclusive_cost_paid')
                ->selectRaw('(exclusive_cost - exclusive_cost_paid) as amount_due')
                ->get()
                ->sum('amount_due');
        }

        $statsData = [
            'countServices' => $countServices + $countPbxServices,
            'countInvoices' => $countInvoices,
            'countEstimates' => $countEstimates,
            'countTickets' => $countTickets,
            'countPayments' => $countPayments,
            'totalAmountDue' => $totalAmountDue,
            'invoiceWithMaxDebit' => $invoiceWithMaxDebit,
            'callRegisterTotalAmount' => $callRegisterTotalAmount,
        ];

        $res = ["success" => true, "response" => ["datamesage" => [
            'customer' => $customer,
            'chartData' => $chartData,
            'statsData' => $statsData,
        ], "message" => "Customer Stats"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Customer Stats");

        return response()->json([
            'customer' => $customer,
            'chartData' => $chartData,
            'statsData' => $statsData,
        ]);
    }

    public function getInvoices(Request $request)
    {
        try {
            $time = microtime(true);
            // Init log
            $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "getInvoices");

            $limit = $request->has('limit') ? $request->limit : 10;

            $customer = Auth::user();

            $invoicesList = Invoice::where('company_id', $customer->company_id)
                ->where('user_id', $customer->id)
                ->when($request->filled('status'), function ($query) use ($request) {
                    return $query
                        ->when($request->status == 'PENDING', function ($q) {
                            return $q->where('due_date', '>', Carbon::now());
                        })
                        ->when($request->status != 'PENDING', function ($q) use ($request) {
                            if ($request->status == 'ARCHIVED') {
                                return $q->onlyTrashed();
                            } else {
                                return $q->where('status', $request->status);
                            }
                        });
                })
                ->with('user')
                ->applyFilters($request->only([
                    'orderByField',
                    'orderBy',
                ]))
                ->paginateData($limit);

            $res = [
                "success" => true,
                "response" => [
                    "datamesage" => [
                        'invoicesList' => $invoicesList,
                        'success' => true,
                    ],
                    "message" => "Lista de facturas asociadas a un cliente",
                ],
            ];

            // Finish log
            LogsDev::finishLog($log, $res, $time, 'D', "Fin de lista de facturas asociadas a un cliente");

            // Module log
            LogsModule::createLog(
                "Profile Customer",
                "Dashboard - Invoices",
                "invoices",
                0,
                $customer->name,
                $customer->email,
                $customer->role,
                $customer->company_id
            );

            return response()->json([
                'invoicesList' => $invoicesList,
                'success' => true,
            ]);
        } catch (Throwable $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getExpenses(Request $request)
    {
        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "getExpenses");

        $customer = Auth::user();

        $limit = $request->has('limit') ? $request->limit : 10;

        $expenses = Expense::with('category', 'creator', 'fields', 'user')
            ->leftJoin('users', 'users.id', '=', 'expenses.user_id')
            ->leftjoin('providers', 'providers.id', '=', 'expenses.providers_id')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->whereCompany($customer->company_id)
            ->where('user_id', $customer->id)
            ->select('expenses.*', 'expense_categories.name', 'users.name as user_name', 'providers.title as provider_title')
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'expenses' => $expenses,
        ], "message" => "Expenses para un cliente"]];

        LogsDev::finishLog($log, $res, $time, 'D', "Expenses para un cliente");

        // Logs por modulo
        LogsModule::createLog(
            "Profile Customer",
            "Dashboard - Expenses",
            "customer/expenses",
            0,
            $customer->name,
            $customer->email,
            $customer->role,
            $customer->company_id
        );

        return response()->json([
            'expenses' => $expenses,
            "success" => true,
        ]);
    }
}
