<?php

namespace Crater\Http\Controllers\V1\Dashboard;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;

//// Models
use Crater\Models\CompanySetting;
use Crater\Models\Estimate;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Crater\Models\User;
use DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request)
    {
        try {
            //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
            $time = microtime(true);
            $log = LogsDev::initLog($request, "", "D", "DashboardController", "__invoke");
            //////////////////

            $invoiceTotals = [];
            $expenseTotals = [];
            $receiptTotals = [];
            $netProfits = [];
            $i = 0;
            $months = [];
            $monthCounter = 0;

            // $excludeexp = Expense::whereNotNull("payment_id")->pluck('payment_id')->toarray();
            $excludeexp = DB::table('expenses')->whereNotNull("payment_id")->pluck('payment_id');

            // calculosde fecha

            $fiscalYear = CompanySetting::getSetting('fiscal_year', $request->header('company'));
            $terms = explode('-', $fiscalYear);
            $currentDate = Carbon::now();
            $monthNow = $currentDate->month;

            // Determinamos si debemos restar un año basándonos en el mes fiscal y la solicitud del año anterior.
            $subtractYears = ($terms[0] > $monthNow) ? 1 : 0;
            $subtractYears += $request->has('previous_year') ? 1 : 0;

            // Aplicamos la lógica a todas las fechas necesarias.
            $startDate = (clone $currentDate)->subYears($subtractYears)->month($terms[0])->startOfMonth();
            $start = (clone $startDate); // No es necesario llamar a startOfMonth() nuevamente.
            $end = (clone $startDate)->endOfMonth(); // Ajustamos al final del mes.

            while ($monthCounter < 12) {
                array_push(
                    $invoiceTotals,
                    Invoice::whereBetween(
                        'invoice_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    )
                        ->whereCompany($request->header('company'))
                        ->whereNull('deleted_at')
                        ->sum('total')
                );
                array_push(
                    $expenseTotals,
                    Expense::whereBetween(
                        'expense_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    )
                        ->where("status", "Active")
                        ->whereCompany($request->header('company'))
                        ->sum('amount')
                );
                //filtro por status de transaccion
                array_push(
                    $receiptTotals,
                    Payment::whereBetween(
                        'payment_date',
                        [$start->format('Y-m-d'), $end->format('Y-m-d')]
                    )->where(function ($query) {
                        $query->where('transaction_status', '=', "Approved")
                            ->orWhere('transaction_status', '=', "Unnaply");
                    })
                        ->whereNotNull('payment_method_id')
                        ->whereNotIn('id', $excludeexp)

                        ->where("applied_credit_customer", 0)
                        ->whereCompany($request->header('company'))
                        ->sum('amount')
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

            $salesTotal = Invoice::whereCompany($request->header('company'))
                ->whereBetween(
                    'invoice_date',
                    [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
                )
                ->where("status", "!=", "DRAFT")
                ->where("status", "!=", "SAVE_DRAFT")
                ->whereNull('deleted_at')
                ->sum('total');

            //filtro por status de transaccion

            $totalReceipts = Payment::whereCompany($request->header('company'))
                ->whereBetween(
                    'payment_date',
                    [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
                )->where(function ($query) {
                    $query->where('transaction_status', '=', "Approved")
                        ->orWhere('transaction_status', '=', "Unapply");
                })
                ->whereNotNull('invoice_id')
                ->whereNotNull('payment_method_id')
                ->whereNotIn('id', $excludeexp)
                ->where("applied_credit_customer", 0)
                ->sum('amount');


            //filtro decreidto añadio
            $totalCredit = Payment::whereCompany($request->header('company'))
                ->whereBetween(
                    'payment_date',
                    [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
                )->where(function ($query) {
                    $query->where('transaction_status', '=', "Approved");
                })
                ->whereNull('invoice_id')
                ->whereNotNull('payment_method_id')
                ->whereNotIn('id', $excludeexp)
                ->where("applied_credit_customer", 0)
                ->sum('amount');

            $totalExpenses = Expense::whereCompany($request->header('company'))
                ->whereBetween(
                    'expense_date',
                    [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
                )
                ->where("status", "Active")
                ->sum('amount');
            $netProfit = (int) $totalReceipts - (int) $totalExpenses;

            $chartData = [
                'months' => $months,
                'invoiceTotals' => $invoiceTotals,
                'expenseTotals' => $expenseTotals,
                'receiptTotals' => $receiptTotals,
                'netProfits' => $netProfits,
            ];

            $customersCount = User::customer()->whereCompany($request->header('company'))->get()->count();

            //// Facturas
            $result = Invoice::whereCompany($request->header('company'))
                ->whereNull('deleted_at')
                ->selectRaw('COUNT(*) as invoicesCount, SUM(due_amount) as totalDueAmount')
                ->first();

            $invoicesCount = $result->invoicesCount;
            $totalDueAmount = $result->totalDueAmount;

            $dueInvoices = Invoice::with('user')->whereCompany($request->header('company'))->whereNull('deleted_at')->where('due_amount', '>', 0)->take(5)->latest()->get();

            ///

            $result2 = DB::table('invoices')
                ->where('company_id', $request->header('company'))
                ->select([
                    DB::raw("COUNT(CASE WHEN deleted_at IS NOT NULL THEN 1 END) AS invoicesCountDeleted"),
                    DB::raw("COUNT(CASE WHEN status = 'DRAFT' AND deleted_at IS NULL THEN 1 END) AS invoicesCountDraft"),
                    DB::raw("COUNT(CASE WHEN status = 'SENT' AND deleted_at IS NULL THEN 1 END) AS invoicesCountSend"),
                    DB::raw("COUNT(CASE WHEN status = 'VIEWED' AND deleted_at IS NULL THEN 1 END) AS invoicesCountView"),
                    DB::raw("COUNT(CASE WHEN status = 'OVERDUE' AND deleted_at IS NULL THEN 1 END) AS invoicesCountOverdue"),
                    DB::raw("COUNT(CASE WHEN status = 'COMPLETED' AND deleted_at IS NULL THEN 1 END) AS invoicesCountCompleted"),
                    DB::raw("COUNT(CASE WHEN paid_status = 'PAID' AND deleted_at IS NULL THEN 1 END) AS invoicesCountPaid"),
                    DB::raw("COUNT(CASE WHEN (paid_status = 'UNPAID' OR paid_status = 'PARTIALLY_PAID') AND deleted_at IS NULL THEN 1 END) AS invoicesCountUnpaid"),
                    DB::raw("COUNT(CASE WHEN paid_status = 'PARTIALLY_PAID' AND deleted_at IS NULL THEN 1 END) AS invoicesCountPartiallyPaid"),
                ])
                ->first();

            // Asignación de las variables
            $invoicesCountDeleted = $result2->invoicesCountDeleted;
            $invoicesCountDraft = $result2->invoicesCountDraft;
            $invoicesCountSend = $result2->invoicesCountSend;
            $invoicesCountView = $result2->invoicesCountView;
            $invoicesCountOverdue = $result2->invoicesCountOverdue;
            $invoicesCountCompleted = $result2->invoicesCountCompleted;
            $invoicesCountpaid = $result2->invoicesCountPaid;
            $invoicesCountunpaid = $result2->invoicesCountUnpaid;
            $invoicesCountppaid = $result2->invoicesCountPartiallyPaid;

            // Estimates
            $result3 = Estimate::where('company_id', $request->header('company'))
                ->select([
                    DB::raw("COUNT(*) AS estimatesCount"),
                    DB::raw("COUNT(CASE WHEN status = 'DRAFT' AND deleted_at IS NULL THEN 1 END) AS estimatesCountDraft"),
                    DB::raw("COUNT(CASE WHEN status = 'SENT' AND deleted_at IS NULL THEN 1 END) AS estimatesCountSent"),
                    DB::raw("COUNT(CASE WHEN status = 'VIEWED' AND deleted_at IS NULL THEN 1 END) AS estimatesCountViewed"),
                    DB::raw("COUNT(CASE WHEN status = 'EXPIRED' AND deleted_at IS NULL THEN 1 END) AS estimatesCountExpired"),
                    DB::raw("COUNT(CASE WHEN status = 'ACCEPTED' AND deleted_at IS NULL THEN 1 END) AS estimatesCountAccepted"),
                    DB::raw("COUNT(CASE WHEN status = 'REJECTED' AND deleted_at IS NULL THEN 1 END) AS estimatesCountRejected"),
                ])
                ->first();

            // Asignación de las variables
            $estimatesCount = $result3->estimatesCount;
            $estimatesCountDraft = $result3->estimatesCountDraft;
            $estimatesCountSent = $result3->estimatesCountSent;
            $estimatesCountViewed = $result3->estimatesCountViewed;
            $estimatesCountExpired = $result3->estimatesCountExpired;
            $estimatesCountAccepted = $result3->estimatesCountAccepted;
            $estimatesCountRejected = $result3->estimatesCountRejected;

            // Estimates

            $estimates = Estimate::with('user')->whereCompany($request->header('company'))->take(5)->latest()->get();

            $balancenocobrado = User::all()->sum("balance");

            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
            $res = ["success" => true, "response" => ["datamesage" => [
                'dueInvoices' => $dueInvoices,
                'estimates' => $estimates,
                'estimatesCount' => $estimatesCount,
                'estimatesCountDraft' => $estimatesCountDraft,
                'estimatesCountSent' => $estimatesCountSent,
                'estimatesCountViewed' => $estimatesCountViewed,
                'estimatesCountExpired' => $estimatesCountExpired,
                'estimatesCountAccepted' => $estimatesCountAccepted,
                'estimatesCountRejected' => $estimatesCountRejected,
                'totalDueAmount' => $totalDueAmount,
                'invoicesCount' => $invoicesCount,
                'invoicesCountpaid' => $invoicesCountpaid,
                'invoicesCountunpaid' => $invoicesCountunpaid,
                'invoicesCountppaid' => $invoicesCountppaid,
                'customersCount' => $customersCount,
                'chartData' => $chartData,
                'salesTotal' => $salesTotal,
                'totalReceipts' => $totalReceipts,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $netProfit,
                'invoicesCountDeleted' => $invoicesCountDeleted,
                'invoicesCountSend' => $invoicesCountSend,
                'invoicesCountView' => $invoicesCountView,
                'invoicesCountOverdue' => $invoicesCountOverdue,
                'invoicesCountCompleted' => $invoicesCountCompleted,
                'invoicesCountDraft' => $invoicesCountDraft,
            ], "message" => "Invoke Dashboard"]];
            LogsDev::finishLog($log, $res, $time, 'D', "Invoke Dashboard");
            //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

            // Logs por modulo
            LogsModule::createLog("dashboard", "List", "admin/dashboard", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);
            $dataDashboard = [
                'dueInvoices' => $dueInvoices,
                'estimates' => $estimates,
                'estimatesCount' => $estimatesCount,
                'estimatesCountDraft' => $estimatesCountDraft,
                'estimatesCountSent' => $estimatesCountSent,
                'estimatesCountViewed' => $estimatesCountViewed,
                'estimatesCountExpired' => $estimatesCountExpired,
                'estimatesCountAccepted' => $estimatesCountAccepted,
                'estimatesCountRejected' => $estimatesCountRejected,
                'totalDueAmount' => $totalDueAmount,
                'invoicesCount' => $invoicesCount,
                'customersCount' => $customersCount,
                'chartData' => $chartData,
                'salesTotal' => $salesTotal,
                'totalReceipts' => $totalReceipts,
                'totalExpenses' => $totalExpenses,
                'netProfit' => $netProfit,
                'invoicesCountpaid' => $invoicesCountpaid,
                'invoicesCountunpaid' => $invoicesCountunpaid,
                'invoicesCountppaid' => $invoicesCountppaid,
                'invoicesCountDeleted' => $invoicesCountDeleted,
                'invoicesCountSend' => $invoicesCountSend,
                'invoicesCountView' => $invoicesCountView,
                'invoicesCountOverdue' => $invoicesCountOverdue,
                'invoicesCountCompleted' => $invoicesCountCompleted,
                'invoicesCountDraft' => $invoicesCountDraft,
                'totalCredit' => $totalCredit,
                'balancenocobradototal' => $balancenocobrado,
            ];

            return response()->json($dataDashboard);
        } catch (\Throwable $th) {
            \Log::debug('Error dashboard index', ['error' => $th]);
        }

    }

    public function fetchInvoicesDue(Request $request)
    {
        $invoices = Invoice::with('user')
            ->applyFilters($request->only([
                'due_date',
                'customer_id',
                'status',
                'due_amount',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->whereNull('deleted_at')
            ->where('due_amount', '>', 0)
            ->take(5)
            ->latest()
            ->get();

        return response()->json([
            'invoices' => $invoices,
        ]);
    }

    public function fetchRecentEstimates(Request $request)
    {
        $estimates = Estimate::with('user')
            ->applyFilters($request->only([
                'expiry_date',
                'customer_id',
                'status',
                'total',
                'orderByField',
                'orderBy',
            ]))
            ->whereCompany($request->header('company'))
            ->whereNull('deleted_at')
            ->take(5)
            ->latest()
            ->get();

        return response()->json([
            'estimates' => $estimates,
        ]);
    }
}
