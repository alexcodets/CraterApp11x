<?php

namespace Crater\Http\Controllers\V1\Expense;

use Auth;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\ExpenseCategoryRequest;
use Crater\Models\ExpenseCategory;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Illuminate\Http\Request;

class ExpenseCategoriesController extends Controller
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
        $log = LogsDev::initLog($request, "", "D", "ExpenseCategoriesController", "__invoke");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $limit = $request->has('limit') ? $request->limit : 5;

        $categories = ExpenseCategory::whereCompany($request->header('company'))
            ->applyFilters($request->only([
                'category_id',
                'search',
                'orderByField',
                'orderBy',
            ]))
            ->latest()
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'categories' => $categories,
        ], "message" => "Expenses index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Expenses index");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "List", "admin/settings/expense-category", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ExpenseCategoryRequest $request)
    {
        $data = $request->validated();
        $data['company_id'] = $request->header('company');
        $category = ExpenseCategory::create($data);

        // Logs por modulo
        LogsModule::createLog("Expenses", "Create", "admin/expenses", $category->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense Category: ".$category->name);

        return response()->json([
            'category' => $category,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\ExpenseCategory $category
     * @return \Illuminate\Http\Response
     */
    public function show(ExpenseCategory $category)
    {

        // Logs por modulo
        LogsModule::createLog("Expenses", "View", "admin/expenses", $category->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense Category: ".$category->name);

        return response()->json([
            'category' => $category,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Crater\Models\ExpenseCategory $ExpenseCategory
     * @return \Illuminate\Http\Response
     */
    public function update(ExpenseCategoryRequest $request, ExpenseCategory $category)
    {
        $category->update($request->validated());


        // Logs por modulo
        LogsModule::createLog("Expenses", "Update", "admin/expenses", $category->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense Category: ".$category->name);


        return response()->json([
            'category' => $category,
            'success' => true,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Crater\ExpensesCategory $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(ExpenseCategory $category)
    {
        if ($category->expenses() && $category->expenses()->count() > 0) {
            return response()->json([
                'success' => false,
            ]);
        }

        // Logs por modulo
        LogsModule::createLog("Expenses", "delete", "admin/expenses", $category->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense Category: ".$category->name);


        $category->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
