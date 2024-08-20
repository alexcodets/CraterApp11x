<?php

namespace Crater\Http\Controllers;

use Crater\Models\ExpenseTemplate;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class ExpenseTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", " index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $limit = $request->has('limit') ? $request->limit : 10;

        $expenseTemplates = ExpenseTemplate::with('category')// , 'creator', 'fields', 'user')
            ->leftJoin('users', 'users.id', '=', 'expense_templates.customer_id')
            ->leftjoin('providers', 'providers.id', '=', 'expense_templates.providers_id')
            ->join('expense_categories', 'expense_categories.id', '=', 'expense_templates.expense_category_id')
            ->applyFilters($request->only([
                'expense_category_id',
                'user_id',
                'providers_id',
                // 'expense_id',
                // 'search',
                'from_date',
                'to_date',
                'template_expense_number',
                // 'customcode',
                'providers_id',
                'orderByField',
                'orderBy',
                // 'status',
            ]))
            ->whereCompany($request->header('company'))
            ->select('expense_templates.*', 'expense_categories.name as category_name', 'users.name as user_name', 'users.customcode as customcode', 'providers.title as provider_title', 'providers.providers_number as providers_number')
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'expenses' => $expenseTemplates,
            'expenseTotalCount' => ExpenseTemplate::count(),
        ], "message" => "Expenses index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Expenses index");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "List", "admin/expenses", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'template_expenses' => $expenseTemplates,
            'expenseTotalCount' => ExpenseTemplate::count(),
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
        try {

            $data = $request->all();
            $data['notification'] = $data["notification"] == true ? 1 : 0;
            ExpenseTemplate::create($data);

            return response()->json([
                'success' => true
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
                'error' => $th
            ]);

        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\ExpenseTemplate  $expenseTemplate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {

            $expenseTemplate = ExpenseTemplate::find($id);

            return response()->json([
                'nextExpenseNumber' => $expenseTemplate->getExpenseNumAttribute(),
                'expense_prefix' => $expenseTemplate->getExpensePrefixAttribute(),
                'expense_template' => $expenseTemplate,
            ]);
        } catch(\Throwable $th) {
            Log::debug($th);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Crater\Models\ExpenseTemplate  $expenseTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request['notification'] = $request["notification"] == true ? 1 : 0;
            ExpenseTemplate::where('id', $id)
            ->update([
                'payment_method_id' => $request['payment_method_id'],
                'items_id' => $request['items_id'],
                'company_id' => $request['company_id'],
                'amount' => $request['amount'],
                'providers_id' => $request['providers_id'],
                'expense_category_id' => $request['expense_category_id'],
                'notification' => $request['notification'],
                'days_after_payment_date' => $request['days_after_payment_date'],
                'initial_status' => $request['initial_status'],
                'term' => $request['term'],
                'expense_date' => $request['expense_date'],
                'description' => $request['description'],
                'subject' => $request['subject'],
                'status' => $request['status'],
                'customer_id' => $request['customer_id'],
                'name' => $request['name'],
            ]);

            return response()->json([
                'success' => true,
            ]);
        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
            ]);

        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\Models\ExpenseTemplate  $expenseTemplate
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        try {
            $expenseTemplate = ExpenseTemplate::find($request->ids[0]);
            $expenseTemplate->delete();

            $res = ["success" => true, "response" => ["datamesage" => [
                'success' => true,
            ], "message" => "Expenses update"]];

            return response()->json([
                'success' => true,
            ]);

        } catch(\Throwable $th) {
            return response()->json([
                'success' => false,
            ]);
        }

    }
}
