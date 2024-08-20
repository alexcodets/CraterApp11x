<?php

namespace Crater\CorePos\Controllers;

use Carbon\Carbon;
use Crater\CorePos\Models\PosCashRegister;
use Crater\CorePos\Models\PosMoney;
use Crater\CorePos\Models\Table;
use Crater\Http\Controllers\Controller;
use Crater\Models\PosSection;
use Crater\Models\User;
use Crater\Models\UserPermisions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class CorePosController extends Controller
{
    ///////////////////////////////  Cash Register //////////////////////////////////

    public function getCashRegisters(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $query = PosCashRegister::query();
        $cash_registers = $query->select(
            'stores.name as store_name',
            'cash_register.id as id',
            'cash_register.open_cash as open_cash',
            'cash_register.name as name',
            'cash_register.description as description',
            'cash_register.device as device'
        )
            ->join('stores', 'cash_register.store_id', '=', 'stores.id')
            ->with('cashHistory')
            // ->addSelect(['status' => function ($query) {$query->select('open')->from('cash_histories')->whereColumn('cash_register_id', 'cash_register.id')->orderBy('id', 'desc')->limit(1);}])
            ->applyFilters($request->only([
                'name',
                'description',
                'store_name',
                'device',
                'orderByField',
                'orderBy',
            ]))
            ->paginateData($limit);

        return response()->json([
            'cash_registers' => $cash_registers,
        ]);
    }

    ///////////////////////////////////////////////////////////////////////// -->
    public function getCashRegister(Request $request, $id)
    {
        $cash_register = PosCashRegister::where('id', $id)->first();

        $users_ids = DB::table('cash_register_users')
            ->where('cash_register_id', $id)
            ->where("deleted_at", null)
            ->pluck('user_id');

        $users = User::whereIn("id", $users_ids)->get();

        $openCashHistory = DB::table('cash_histories')->where('cash_register_id', $id)->get()->last();

        return response()->json([
            'cash_register' => $cash_register,
            'last_cash_register' => $openCashHistory,
            'users' => $users
        ]);
    }

    public function getUsers(Request $request)
    {
        // $users_super_admin = User::where('role', 'super admin')->orWhere('role2', 'super admin')->pluck('id');
        // $users_with_permisions = UserPermisions::whereIn('user_id', $users_super_admin)->where('module', 'corePOS_dashboard')->where('access', 1)->pluck('user_id');
        // $users = User::whereIn('id', $users_with_permisions)->get();
        $companyId = auth()->user()->company->id;
        $users = User::where('company_id', $companyId)->where('role', 'super admin')->get();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function addCashRegister(Request $request)
    {
        // INSERT (cash_register)
        DB::table('cash_register')->insert([
            'name' => $request['name'],
            'description' => $request['description'],
            'customer_id' => $request['customer_id'],
            'store_id' => $request['store_id'],
            'device' => $request['device'],
            'open_cash' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);

        $cash_register = PosCashRegister::latest()->first();

        if (! is_null($request['users_id'])) {
            if (count($request['users_id']) > 0) {
                foreach ($request['users_id'] as $user_id) {
                    // INSERT (cash_register_users)
                    DB::table('cash_register_users')->insert([
                        'cash_register_id' => $cash_register->id,
                        'user_id' => $user_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'deleted_at' => null,
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Record created successfully.'
        ]);
    }

    public function updateCashRegister(Request $request)
    {
        // UPDATE cash_register
        $cash_register = PosCashRegister::find($request['id']);
        if ($cash_register) {
            $cash_register->name = $request['name'];
            $cash_register->description = $request['description'];
            $cash_register->device = $request['device'];
            $cash_register->customer_id = $request['customer_id'];
            $cash_register->store_id = $request['store_id'];
            $cash_register->save();
        }

        ////////////////////////  UPDATE cash_register_users (Delete and Create)  ////////////////////////////
        $cash_register_users_ids = DB::table('cash_register_users')->where("cash_register_id", $request['id'])
            ->where('deleted_at', null)
            ->pluck('id');

        // Delete
        if (count($cash_register_users_ids) > 0) {
            foreach ($cash_register_users_ids as $id) {
                DB::table('cash_register_users')->where('id', $id)->delete();
            }
        }
        // Create
        if (! is_null($request['users_id'])) {
            // Create
            foreach ($request['users_id'] as $user_id) {
                DB::table('cash_register_users')->insert([
                    'cash_register_id' => $request['id'],
                    'user_id' => $user_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null,
                ]);
            }
        }
        ////////////////////////////////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully.'
        ]);
    }

    public function deleteCashRegister(Request $request, $id)
    {
        // Delete cash_register
        $cash_register = PosCashRegister::where('id', $id)->delete();

        ///////////////////////////  DELETE cash_register_users   /////////////////////////////////
        $cash_register_users_ids = DB::table('cash_register_users')->where("cash_register_id", $id)
            ->where('deleted_at', null)
            ->pluck('id');

        if (count($cash_register_users_ids) > 0) {
            foreach ($cash_register_users_ids as $id) {
                DB::table('cash_register_users')->where('id', $id)->delete();
            }
        }
        ///////////////////////////

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully.'
        ]);
    }

    /////////////////////////////////  />  //////////////////////////////////////////

    ///////////////////////////////  Money  /////////////////////////////////////////

    public function getMoney(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $pos_money = PosMoney::with(['paymentMethods'])
            ->join("currencies", "pos_money.currency_id", "=", "currencies.id")
            ->applyFilters($request->only([
                'orderByField',
                'orderBy',
                'money_name',
                'is_coin',
                'currency_name',
            ]))
            ->select("pos_money.*", "currencies.name as currency_name") // Alias para el campo "name" de la tabla "currencies"
            ->paginateData($limit);

        return response()->json([
            'pos_money' => $pos_money,
        ]);
    }

    public function addMoney(Request $request)
    {
        // create Record
        $register = DB::table('pos_money')->insert([
            'name' => $request['name'],
            'amount' => $request['amount'],
            'is_coin' => $request['is_coin'],
            'currency_id' => $request['currency_id'],
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'deleted_at' => null,
        ]);

        if ($register) {
            // pos_money_payment_methods
            $pos_money = PosMoney::latest()->first();

            if (count($request["payment_methods_ids"]) > 0) {
                foreach ($request["payment_methods_ids"] as $pm_id) {
                    DB::table('pos_money_payment_methods')->insert([
                        'pos_money_id' => $pos_money->id,
                        'payment_method_id' => $pm_id,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }
            }
            //
        }

        return response()->json([
            'success' => true,
            'message' => 'Record created successfully.'
        ]);
    }

    public function updateMoney(Request $request)
    {
        $pos_money = PosMoney::find($request['id']);

        if ($pos_money) {
            $pos_money->name = $request['name'];
            $pos_money->amount = $request['amount'];
            $pos_money->is_coin = $request['is_coin'];
            $pos_money->currency_id = $request['currency_id'];
            $pos_money->save();
        }

        // Delete
        DB::table('pos_money_payment_methods')->where("pos_money_id", $request['id'])->delete();
        // Create
        if (count($request["payment_methods_ids"]) > 0) {
            foreach ($request["payment_methods_ids"] as $pm_id) {
                DB::table('pos_money_payment_methods')->insert([
                    'pos_money_id' => $request['id'],
                    'payment_method_id' => $pm_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Record updated successfully.'
        ]);
    }

    public function deleteMoney(Request $request, $id)
    {
        $res = PosMoney::where('id', $id)->delete();

        DB::table('pos_money_payment_methods')->where("pos_money_id", $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Record deleted successfully.'
        ]);
    }
    ///////////////////////////////  />  /////////////////////////////////////////

    public function getCashRegistersUser()
    {
        try {


            $userId = auth()->user()->id;
            // $cashRegisterAllow = DB::table('cash_register')
            //     ->join('cash_register_users', 'cash_register.id', '=', 'cash_register_users.cash_register_id')
            //     ->join('stores', 'cash_register.store_id', '=', 'stores.id')
            //     ->join('cash_histories', 'cash_register.id', '=', 'cash_histories.cash_register_id')
            //     ->where('cash_register_users.user_id', $userId)
            //     ->where('cash_histories.open', 1)
            //     ->select('cash_register.*', 'stores.name as store_name') // Selecting 'cash_register' fields and 'name' from 'stores'
            //     ->get();

            $cashRegisterAllow = DB::table('cash_register')
                ->join('cash_register_assign_users', 'cash_register.id', '=', 'cash_register_assign_users.cash_register_id')
                ->join('stores', 'cash_register.store_id', '=', 'stores.id')
                ->join('cash_histories', 'cash_register.id', '=', 'cash_histories.cash_register_id')
                ->where('cash_register_assign_users.user_id', $userId)
                ->where('cash_histories.open', 1)
                ->select('cash_register.*', 'stores.name as store_name', 'cash_histories.id  as cash_history_id')
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cashRegisterAllow
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \Log::debug($th);
        }
    }

    // metodo para rellenar tablas
    public function getCashRegistersUserall()
    {
        try {

            $userId = auth()->user()->id;

            \Log::debug("pop 1");

            $cashRegisterAllow = DB::table('cash_register')


                ->select('cash_register.*')
                ->distinct()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $cashRegisterAllow
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            \Log::debug($th);
        }
    }

    public function getInvoicesCashPayment(Request $request)
    {
        try {
            //code...
            // $date = Carbon::createFromFormat('Y-m-d', $request['open_date']);
            $cashRegisterId = $request['cash_register_id'];

            $invoicesAmount = DB::table('cash_register_invoice')
                ->select(DB::raw('SUM(payments_payment_methods.received) as received'), DB::raw('SUM(payments_payment_methods.returned) as returned'))
                ->join('invoices', 'cash_register_invoice.invoice_id', '=', 'invoices.id')
                ->join('payments',  'payments.invoice_id', '=', 'invoices.id')
                ->join('payments_payment_methods', 'payments.id', '=', 'payments_payment_methods.payment_id')
                ->join('payment_methods', 'payments_payment_methods.payment_method_id', '=', 'payment_methods.id')
                ->where('cash_register_invoice.cash_register_id', $cashRegisterId)
                ->where('invoices.paid_status', 'PAID')
                ->where('payment_methods.only_cash', 1)
                ->whereBetween('invoices.created_at', [$request['open_date'], Carbon::now()])
                ->get();

            $amountIncome = DB::table('cash_register_cash_histories')
                ->where('cash_register_id', $cashRegisterId)
                ->where('type', 'I')
                ->whereBetween('created_at', [$request['open_date'], Carbon::now()])
                ->sum('amount');
            $amountWithdrawal = DB::table('cash_register_cash_histories')
                ->where('cash_register_id', $cashRegisterId)
                ->where('type', 'R')
                ->whereBetween('created_at', [$request['open_date'], Carbon::now()])
                ->sum('amount');

            // $users = DB::table('cash_register_users')->where('cash_register_id', $cashRegisterId)->get();

            return response()->json([
                'success' => true,
                'invoices_amount' => $invoicesAmount->first(),
                'amount_income' => $amountIncome,
                'amount_withdrawal' => $amountWithdrawal,
            ]);
        } catch (\Throwable $th) {
            Log::debug($th);

            return response()->json([
                'success' => false,

            ]);
        }
    }

    public function getUserAssignCashRegister(Request $request)
    {
        $data = $request->all();
        $cashRegisterId = $data['data']['cash_register_id'];
        $users = DB::table('cash_register_users')
            ->select('users.name AS name', 'users.id AS id')
            ->join('users', 'cash_register_users.user_id', '=', 'users.id')
            ->where('cash_register_id', $cashRegisterId)->get();

        return response()->json([
            'users' => $users,
            'success' => true
        ]);
    }

    public function userAssignCashRegister(Request $request)
    {
        $data = $request->all();
        $cashRegisterId = $data['params2']['cash_register_id'];
        $cashHistoryId = $data['params2']['cash_history_id'];
        $users = DB::table('cash_register_assign_users')
            ->select('users.name AS name', 'users.id AS id')
            ->join('users', 'cash_register_assign_users.user_id', '=', 'users.id')
            ->where('cash_register_id', $cashRegisterId)
            ->where('cash_history_id', $cashHistoryId)
            ->get();

        return response()->json([
            'users' => $users,
            'success' => true
        ]);
    }

    public function getSections(Request $request)
    {

        $limit = $request->has('limit') ? $request->limit : 5;
        $sections = PosSection::where('company_id', auth()->user()->company->id)
        ->applyFilters($request->only([
            'orderByField',
            'orderBy',
        ]))
        ->paginateData($limit);

        return response()->json([
            'success' => true,
            'message' => 'sections',
            'sections' => $sections
        ]);
    }

    public function createSections(Request $request)
    {

        $data = $request->all();

        $exists = PosSection::where('name', $data['name'])->where('company_id', auth()->user()->company->id)->exists();
        if($exists) {
            return response()->json([
                'success' => true,
                'exists' => true,
                'message' => 'The record exists in DB',
            ]);
        } else {
            $data['company_id'] = auth()->user()->company->id;

            $sections = DB::table('pos_sections')->insert($data);

            return response()->json([
                'success' => true,
                'exists' => false,
                'message' => 'section create',
                'sections' => $sections
            ]);
        }
    }

    public function updateSections(Request $request)
    {

        DB::table('pos_sections')->where('id', $request['id'])->update([
            'name' => $request['name']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'sections',
        ]);
    }
}
