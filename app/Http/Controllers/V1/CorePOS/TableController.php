<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Crater\CorePos\Models\Table;
use Crater\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;
        $query = Table::query();
        $query = $query->with(['cashRegister','user']);


        if ($request->has('name')) {
            $nameFilter = $request->name;
            $query->where('tables.name', 'LIKE', '%'.$nameFilter.'%');
        }

        $tables = $query->paginateData($limit);

        return response()->json([
            'tables' => $tables,
        ]);
    }

    public function getTables(Request $request)
    {

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
                    $field = 'tables.name';

                    break;
                case 'user_id':
                    $field = 'tables.user_id';

                    break;
                case 'cash_register':
                    $field = 'cash_register.name';

                    break;
                default:
                    $field = 'tables.id';

                    break;
            }

        }

        $limit = $request->has('limit') ? $request->limit : 10;
        $query = Table::with(['cashRegister','user']);


        if ($request->has('name')) {
            $nameFilter = $request->name;
            $query->where('tables.name', 'LIKE', '%'.$nameFilter.'%');
        }

        $tables = $query->orderBy($field, $orderBy)->paginateData($limit);

        return response()->json([
            'tables' => $tables,
        ]);
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
        $table = DB::table('tables')->insertGetId([
            'name' => $request['name'],
            'user_id' => auth()->user()->id
        ]);

        foreach($request['cash_registers'] as $cashRegister) {
            DB::table('cash_register_table_table_pivot')->insert([
                'cash_register_id' => $cashRegister['id'],
                'table_id' => $table
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Table created successfully.',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $table = Table::with('cashRegister')->where('id', $id)->whereNull('deleted_at')->first();

            return response()->json([
                'success' => true,
                'table' => $table
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'success' => true
            ]);
        }
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
        $table = Table::findOrFail($id);
        $table->name = $request->input('name');
        $table->save();

        DB::table('cash_register_table_table_pivot')->where('table_id', $table->id)->delete();

        foreach($request['cash_registers'] as $cashRegister) {
            DB::table('cash_register_table_table_pivot')->insert([
                'cash_register_id' => $cashRegister['id'],
                'table_id' => $id
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Table updated successfully.',
            'table' => $table,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {

            $holdInvoices = DB::table('hold_tables')->where('table_id', $id)->exists();

            if($holdInvoices) {
                return response()->json([
                    'success' => false,
                    'message' => 'record_cannot_delete'
                ]);
            }

            Table::where('id', $id)->delete();

            return response()->json([
                'success' => true,
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => true,
                'message' => 'Error'
            ]);

        }
    }

    public function getTablesCashRegister($cashRegisterId)
    {
        try {
            // $tables = DB::table('cash_register_table_table_pivot')
            // // ->join('tables', 'cash_register_table_table_pivot.table_id', '=', 'tables.id')
            // // ->where('cash_register_id', $cashRegisterId)
            // ->get();
            $tables = DB::table('tables')->get();

            return response()->json([
                'success' => true,
                'data' => $tables,
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => true,
                'data' => $tables,
            ]);
        }
    }
}
