<?php

namespace Crater\Http\Controllers\V1\Customer;

use Crater\Http\Controllers\Controller;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerPaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {

        $time = microtime(true);

        $log = LogsDev::initLog($request, "", "D", "CustomerProfileController", "getPayments");

        $customer = Auth::user();

        $payments = Payment::with(['user', 'invoice', 'paymentMethod', 'creator'])
            ->join('users', 'users.id', '=', 'payments.user_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->leftJoin('payment_methods', 'payment_methods.id', '=', 'payments.payment_method_id')
            ->applyFilters($request->only([
                'transaction_status',
                'payment_number',
                'invoice_number',
                'from_date',
                'to_date',
                'orderByField',
                'orderBy'
            ]))->when($request->get('search'), function (Builder $query) use ($request) {

                return $query->where(function (Builder $query) use ($request) {
                    return $query->where('payment_number', 'LIKE', '%'.$request->get('search').'%')
                        ->orWhere('invoice_number', 'LIKE', '%'.$request->get('search').'%');
                });

            })
            ->whereCompany($customer->company_id)
            ->where('payments.user_id', $customer->id)
            ->select('payments.*', 'users.name', 'invoices.invoice_number', 'payment_methods.name as payment_mode')
            ->latest()
            ->paginateData($request->get('limit', 10));

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = [
            "success" => true, "response" => [
                'payments' => $payments,
            ], "message" => "Payments asociados a un cliente"
        ];

        LogsDev::finishLog($log, $res, $time, 'D', "Payments asociados a un cliente");

        // Logs por modulo
        LogsModule::createLog(
            "Customer Dashboard",
            "Get Payments",
            "customer/payments",
            0,
            $customer->name,
            $customer->email,
            $customer->role,
            $customer->company_id
        );

        return response()->json([
            'payments' => $payments,
            "success" => true,
        ]);

    }
}
