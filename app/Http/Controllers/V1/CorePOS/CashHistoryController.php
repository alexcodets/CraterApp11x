<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Carbon\Carbon;
use Crater\CorePos\Models\PosCashRegister;
use Crater\Http\Controllers\Controller;
use Crater\Models\CashHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class CashHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            Log::debug($request->all());
            $data = $request->all();
            $cashHistories = CashHistory::select('cash_histories.*', 'users.name AS user_name')
                ->join('users', 'cash_histories.user_id', '=', 'users.id')
                ->where('cash_histories.cash_register_id', $data['cash_register_id'])->latest()->get();

            return response()->json([
                'data' => $cashHistories,
                'success' => true
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => false
            ]);
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
        try {
            $data = $request->all();
            $data['created_at'] = Carbon::now();
            if ($data['open']) {

                $cashHistoryId = CashHistory::insertGetId([
                    "open" => $data["open"],
                    "ref" => $data["ref"],
                    "cash_received" => $data["cash_received"],
                    "initial_amount" => $data["initial_amount"],
                    "open_note" => $data["open_note"],
                    "open_date" => $data["open_date"],
                    "final_amount" => $data["final_amount"],
                    "close_note" => $data["close_note"],
                    "close_date" => $data["close_date"],
                    "cash_register_id" => $data["cash_register_id"],
                    "created_at" => Carbon::now(),
                    "user_id" => auth()->user()->id
                ]);


                PosCashRegister::where('id', $data['cash_register_id'])->update([
                    'open_cash' => 1
                ]);
                foreach ($data['users'] as $user) {

                    DB::table('cash_register_assign_users')->insert([
                        'cash_register_id' => $data["cash_register_id"],
                        'user_id' => $user['id'],
                        'created_at' => Carbon::now(),
                        'cash_history_id' => $cashHistoryId
                    ]);
                }
            } else {
                CashHistory::where('cash_register_id', $data['cash_register_id'])->where('id', $data['id'])->update([
                    'final_amount' => $data['final_amount'],
                    'close_note' => $data['close_note'],
                    'close_date' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'open' => 0
                ]);

                PosCashRegister::where('id', $data['cash_register_id'])->update([
                    'open_cash' => 0
                ]);

                $cashRegisterInvoices = DB::table('cash_register_invoice')
                    ->where('cash_register_id', $data['cash_register_id'])
                    ->whereNull('cash_history_id')
                    ->get();

                foreach ($cashRegisterInvoices as $cashRegisterInvoice) {
                    DB::table('cash_register_invoice')->where('id', $cashRegisterInvoice->id)
                        ->update([
                            'cash_history_id' => $data['id']
                        ]);
                }

                $amountPayments = DB::table('payments')
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->join('cash_register_invoice', 'invoices.id', '=', 'cash_register_invoice.invoice_id')
                    ->where('cash_register_invoice.cash_register_id', $data['cash_register_id'])
                    ->where('cash_register_invoice.cash_history_id', $data['id'])
                    ->get();

                CashHistory::where('id', $data['id'])
                    ->update([
                        'other_income' => $amountPayments->sum('amount')
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cash History created'
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
                'message' => 'Error'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cashHistory = CashHistory::where('id', $id)->get();

        return response()->json([
            'success' => true,
            'cash_history' => $cashHistory
        ]);
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

    public function getCashRegisterHistories(Request $request)
    {
        try {

            $limit = $request->has('limit') ? $request->limit : 5;
            $cashHistory = CashHistory::where('cash_register_id', $request['cashRegisterId'])
                ->applyFilters($request->only([
                    'orderByField',
                    'orderBy',
                ]))
                ->paginateData($limit);

            return response()->json([
                'success' => true,
                'data' => $cashHistory,
                "message" => 'Histories'
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                "message" => 'Error'
            ]);
        }
    }

    public function storeIncomeWithdrawalCash(Request $request)
    {
        try {
            $data = $request->all();
            $data['creator_id'] = auth()->user()->id;
            $data['created_at'] = Carbon::now();
            $data['amount'] = $data['amount'] * 100;
            DB::table('cash_register_cash_histories')->insert($data);

            return response()->json([
                'success' => true,
                "message" => 'Success'
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
                "message" => 'Error'
            ]);
        }
    }

    public function showIncomeWithdrawalCash(Request $request)
    {
        try {
            $data = $request->all();

            $response = DB::table('cash_register_cash_histories')
                ->select(
                    'cash_register_cash_histories.amount as amount',
                    'cash_register_cash_histories.type as type',
                    'cash_register_cash_histories.created_at as created_at',
                    'cash_histories.ref',
                    'users.name as user_name'
                )
                ->join('users', 'users.id', '=', 'cash_register_cash_histories.creator_id')
                ->join('cash_histories', 'cash_histories.id', '=', 'cash_register_cash_histories.cash_histories_id')
                ->where('cash_register_cash_histories.cash_register_id', $data['cash_register_id'])
                ->orderBy('cash_register_cash_histories.created_at', 'desc')
                ->paginate(5);

            return response()->json([
                'success' => true,
                'data' => $response,
                "message" => 'Success'
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,
                "message" => 'Error'
            ]);
        }
    }

    public function confirmOpenCashRegister($id)
    {
        CashHistory::where('id', $id)->update([
            'confirmed' => 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cash Register Open confirmated'
        ]);
    }
}
