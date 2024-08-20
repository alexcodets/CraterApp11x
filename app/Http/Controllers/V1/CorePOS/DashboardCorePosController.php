<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Crater\Http\Controllers\Controller;
use Crater\Models\CashHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class DashboardCorePosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $companyId = auth()->user()->company->id;

        $invoices = DB::table('invoices')->where('company_id', $companyId)->where('is_invoice_pos', 1)->get();
        $amountInvoices = $invoices->sum('total');
        $quantityInvoices = $invoices->count();

        $quantityCashRegister = DB::table('cash_register')->count();

        $quantityPayments = DB::table('payments')->whereIn('invoice_id', $invoices->pluck('id'))->count();

        $data = [
            'invoices_amount' => $amountInvoices,
            'quantity_invoices' => $quantityInvoices,
            'quantity_cash_register' => $quantityCashRegister,
            'quantity_payments' => $quantityPayments,
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    public function dataCashHistories(Request $request)
    {

        $limit = $request->has('limit') ? $request->limit : 10;

        try {

            $cashHistories = CashHistory::select('cash_histories.*', 'cash_register.name as cash_register_name', 'cash_register.id as cash_register_id')
                ->join('cash_register', 'cash_histories.cash_register_id', '=', 'cash_register.id')
                ->applyFilters($request->only([
                    'orderByField',
                    'orderBy',
                ]))
                ->latest()
                ->paginateData($limit);

            return response()->json([
                'success' => true,
                'data' => $cashHistories
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);
        }
    }

    public function dataIncomeWithdrawalCash(Request $request)
    {
        try {

            $filters = $request->only([
                'orderByField',
                'orderBy',
            ]);

            $filters = collect($filters);

            if ($filters->get('orderByField') || $filters->get('orderBy')) {
                // $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'cash_register_cash_histories.id';
                $field = '';
                $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';

                switch ($filters->get('orderBy')) {
                    case 'cash_register_name':
                        $field = 'cash_register.name';

                        break;
                    case 'ref':
                        $field = 'cash_histories.ref';

                        break;
                    case 'created_at':
                        $field = 'cash_register_cash_histories.created_at';

                        break;
                    case 'user_name':
                        $field = 'users.name';

                        break;
                    case 'amount':
                        $field = 'cash_register_cash_histories.amount';

                        break;
                    case 'id':
                        $field = 'cash_register_cash_histories.id';

                        break;

                    default:
                        $field = 'cash_register_cash_histories.id';

                        break;
                }

            }

            $limit = $request->has('limit') ? $request->limit : 10;

            $cashRegisterCashHistory = DB::table('cash_register_cash_histories')
                ->select(
                    'cash_register_cash_histories.*',
                    'cash_register.name as cash_register_name',
                    'cash_register.id as cash_register_id',
                    'users.name as user_name',
                    'cash_histories.ref as ref'
                )
                ->join('cash_register', 'cash_register_cash_histories.cash_register_id', '=', 'cash_register.id')
                ->join('users', 'cash_register_cash_histories.creator_id', '=', 'users.id')
                ->join('cash_histories', 'cash_register_cash_histories.cash_histories_id', '=', 'cash_histories.id')
                ->orderBy($field, $orderBy)
                ->paginate($limit);

            return response()->json([
                'success' => true,
                'data' => $cashRegisterCashHistory
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
