<?php

namespace Crater\Http\Controllers\V1\Customer;

use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerPackage;
use Crater\Models\Expense;
use Crater\Models\Invoice;
use Crater\Models\LogsDev;
use Crater\Models\Payment;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerStatsController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, User $customer)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "CustomerStatsController", "__invoke");
        //////////////////

        $i = 0;
        $months = [];
        $invoiceTotals = [];
        $expenseTotals = [];
        $receiptTotals = [];
        $netProfits = [];
        $monthCounter = 0;

        $excludeexp = Expense::whereNotNull("payment_id")->pluck('payment_id')->toarray();

        $fiscalYear = CompanySetting::getSetting('fiscal_year', $request->header('company'));
        $terms = explode('-', $fiscalYear);

        // Creamos una sola instancia de Carbon para la fecha actual.
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
                    ->where('status', '!=', 'SAVE_DRAFT')
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
                    ->where("status", "Active")
                    ->whereCompany($request->header('company'))
                    ->whereUser($customer->id)
                    ->sum('amount') ?? 0
            );
            array_push(
                $receiptTotals,
                Payment::whereBetween(
                    'payment_date',
                    [$start->format('Y-m-d'), $end->format('Y-m-d')]
                )
                    ->whereCompany($request->header('company'))
                    ->whereCustomer($customer->id)
                    ->whereNotNull('payment_method_id')
                    ->whereNotIn('id', $excludeexp)

                    ->where("applied_credit_customer", 0)
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

        $salesTotal = Invoice::whereCompany($request->header('company'))
            ->whereBetween(
                'invoice_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->where('status', '!=', 'SAVE_DRAFT')
            ->whereNull('deleted_at')
            ->whereCustomer($customer->id)
            ->sum('total');

        $totalReceipts = Payment::whereCompany($request->header('company'))
            ->whereBetween(
                'payment_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )->where(function ($query) {
                $query->where('transaction_status', '=', "Approved")
                    ->orWhere('transaction_status', '=', "Unapply");
            })
            ->whereNotNull('invoice_id')
            ->whereNotIn('id', $excludeexp)

            ->where("applied_credit_customer", 0)
            ->whereCustomer($customer->id)
            ->sum('amount');

        $paymentsCredits = Payment::whereCompany($request->header('company'))
            ->whereBetween(
                'payment_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )->where(function ($query) {
                $query->where('transaction_status', '=', "Approved");
            })
            ->whereNull('invoice_id')
            ->whereNotIn('id', $excludeexp)

            ->where("applied_credit_customer", 0)
            ->whereCustomer($customer->id)
            ->sum('amount');

        $totalExpenses = Expense::whereCompany($request->header('company'))
            ->whereBetween(
                'expense_date',
                [$startDate->format('Y-m-d'), $start->format('Y-m-d')]
            )
            ->where("status", "Active")
            ->whereUser($customer->id)
            ->sum('amount');

        $netProfit = (int) $totalReceipts - (int) $totalExpenses;

        $balanceTotal = $customer->balance;

        $callRegisterTotalAmount = 0;

        // Obtenemos los IDs de los servicios de PBX del cliente con estado 'A'.
        $pbx_service_ids = PbxServices::where('customer_id', $customer->id)
            ->where('status', 'A')
            ->pluck('id');

        // Calculamos la suma total de la cantidad debida directamente en la base de datos.
        $callRegisterTotalAmount = CallDetailRegisterTotal::whereIn('pbx_services_id', $pbx_service_ids)
            ->whereNull('invoice_id')
            ->whereColumn('exclusive_cost', '>', 'exclusive_cost_paid')
            ->sum(DB::raw('exclusive_cost - exclusive_cost_paid'));

        // Multiplicamos el total por 100 para obtener el monto en centavos o como sea necesario.
        $callRegisterTotalAmount *= 100;

        // Definimos un query base para las facturas del cliente que no están eliminadas.
        $baseQuery = Invoice::whereNull('deleted_at')
            ->whereCustomer($customer->id);

        // Calculamos el total de las facturas vencidas.
        $invoiceDue = (clone $baseQuery)->whereNotIn('status', ['DRAFT', 'SAVE_DRAFT', 'COMPLETED'])
            ->where('paid_status', '!=', 'PAID')
            ->sum('due_amount');

        // Calculamos el total de las facturas enviadas.
        $invoiceSent = (clone $baseQuery)->where('status', 'SENT')
            ->where('paid_status', '!=', 'PAID')
            ->sum('due_amount');

        // Calculamos el total de las facturas atrasadas.
        $invoiceOverdue = (clone $baseQuery)->where('status', 'OVERDUE')
            ->where('paid_status', '!=', 'PAID')
            ->sum('due_amount');

        // Calculamos el total de las facturas no pagadas.
        $invoiceUnpaid = (clone $baseQuery)->whereNotIn('status', ['DRAFT', 'SAVE_DRAFT'])
            ->where('paid_status', 'UNPAID')
            ->sum('due_amount');

        $accountbalance = $callRegisterTotalAmount + $invoiceDue;
        // Primero, obtenemos los IDs de los paquetes y servicios de PBX del cliente.
        $service_ids = CustomerPackage::where('customer_id', $customer->id)->pluck('id');
        $pbx_service_ids = PbxServices::where('customer_id', $customer->id)->pluck('id');

        // Definimos una consulta base para las facturas que no están en estado DRAFT, SAVE_DRAFT, COMPLETED, ni pagadas y que no están eliminadas.
        $baseQuery = Invoice::whereNotIn('status', ['DRAFT', 'SAVE_DRAFT', 'COMPLETED'])
            ->where('paid_status', '!=', 'PAID')
            ->whereNull('deleted_at')
            ->whereCustomer($customer->id);

        // Clonamos la consulta base y calculamos el total de las facturas para los paquetes de servicios del cliente.
        $servicetotalQuery = (clone $baseQuery);
        $servicetotal = $servicetotalQuery->whereIn('customer_packages_id', $service_ids)->sum('due_amount');

        // Clonamos la consulta base nuevamente y calculamos el total de las facturas para los servicios de PBX del cliente.
        $pbxservicetotalQuery = (clone $baseQuery);
        $pbxservicetotal = $pbxservicetotalQuery->whereIn('pbx_service_id', $pbx_service_ids)->sum('due_amount');

        $numberAsString = number_format($callRegisterTotalAmount / 100, 2);
        $chartData = [
            'accountBalance' => $accountbalance,
            'callRegisterTotalAmount' => $numberAsString,
            'months' => $months,
            'invoiceTotals' => $invoiceTotals,
            'invoiceDue' => $invoiceDue,
            'invoiceOverdue' => $invoiceOverdue,
            'invoiceSent' => $invoiceSent,
            'invoiceUnpaid' => $invoiceUnpaid,
            'expenseTotals' => $expenseTotals,
            'receiptTotals' => $receiptTotals,
            'netProfit' => $netProfit,
            'netProfits' => $netProfits,
            'salesTotal' => $salesTotal,
            'totalReceipts' => $totalReceipts,
            'totalExpenses' => $totalExpenses,
            'balanceTotal' => $balanceTotal,
            'callRegisterTotalAmount' => $numberAsString,
            'servicetotal' => $servicetotal,
            'pbxservicetotal' => $pbxservicetotal,
            'paymentsCredits' => $paymentsCredits,
        ];

        $customer = User::with([
            'billingAddress',
            'shippingAddress',
            'billingAddress.country',
            'shippingAddress.country',
            'billingAddress.state',
            'shippingAddress.state',
            'currency',
            'fields.customField',
            'notes',
        ])->find($customer->id);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'customer' => $customer,
            'chartData' => $chartData,
        ], "message" => "Customer Stats"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Customer Stats");
        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,

        return response()->json([
            'customer' => $customer,
            'chartData' => $chartData,
        ]);
    }
}
