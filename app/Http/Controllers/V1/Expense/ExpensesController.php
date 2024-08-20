<?php

namespace Crater\Http\Controllers\V1\Expense;

use Auth;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Http\Requests\DeleteExpensesRequest;
use Crater\Http\Requests\ExpenseRequest;
use Crater\Models\Expense;
use Crater\Models\ExpenseCategory;
use Crater\Models\LogsDev;
use Crater\Models\LogsModule;
use Crater\Models\PaymentMethod;
use Crater\Models\TaxType;
use Illuminate\Http\Request;
use Log;

class ExpensesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", " index");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $limit = $request->input('limit', 10);

        $expenses = Expense::with('category', 'creator', 'fields', 'user', 'provider')
            ->leftJoin('users', 'users.id', '=', 'expenses.user_id')
            ->leftjoin('providers', 'providers.id', '=', 'expenses.providers_id')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->applyFilters($request->only([
                'expense_category_id',
                'user_id',
                'expense_id',
                'search',
                'from_date',
                'to_date',
                'expense_number',
                'customcode',
                'providers_id',
                'orderByField',
                'orderBy',
                'status',
                'subject'
            ]))
            ->whereCompany($request->header('company'))
            ->select('expenses.*', 'expense_categories.name', 'users.name as user_name', 'providers.title as provider_title')
            ->paginateData($limit);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'expenses' => $expenses,
            'expenseTotalCount' => Expense::count(),
        ], "message" => "Expenses index"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Expenses index");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "List", "admin/expenses", 0, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id);

        return response()->json([
            'expenses' => $expenses,
            'expenseTotalCount' => Expense::count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ExpenseRequest $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", "store");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $expense = Expense::createExpense($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'expense' => $expense,
            'success' => true,
        ], "message" => "Expenses store"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Expenses store");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "Create", "admin/expenses/create", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->id);

        return response()->json([
            'expense' => $expense,
            'success' => true,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Crater\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, Expense $expense)
    {
        $request->merge(['Expense' => $expense]);
        $expense->load('creator', 'fields.customField', 'provider', 'category', 'user', 'item');

        // invoices
        $expense_invoices = \DB::table('expense_invoices')->where('expense_id', $expense->id)->get();
        foreach ($expense_invoices as $invoice) {
            $taxes_id = \DB::table('expense_invoices_tax_types')
                            ->where('expense_invoice_id', $invoice->id)
                            ->pluck('tax_type_id');

            if($taxes_id->isNotEmpty()) {
                $taxes = TaxType::whereIn('id', $taxes_id)
                                 ->whereNull("deleted_at")
                                 ->get();

                $invoice->taxes = $taxes;
            } else {
                $invoice->taxes = [];
            }
        }
        $expense->invoices = $expense_invoices;
        //

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'nextExpenseNumber' => $expense->getExpenseNumAttribute(),
            'expense_prefix' => $expense->getExpensePrefixAttribute(),
            'expense' => $expense,
        ], "message" => "Expenses store"]];

        // Logs por modulo
        LogsModule::createLog("Expenses", "View", "admin/expenses/id/edit", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->id);

        return response()->json([
            'nextExpenseNumber' => $expense->getExpenseNumAttribute(),
            'expense_prefix' => $expense->getExpensePrefixAttribute(),
            'expense' => $expense,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Crater\Models\Expense $expense
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ExpenseRequest $request, Expense $expense)
    {
        $time = microtime(true);
        $request2 = $request;
        $request2->merge(['Expense' => $expense]);
        //  $log = LogsDev::initLog($request2, "", "D", "ExpensesController", "show");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $expense->updateExpense($request);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'expense' => $expense,
            'success' => true,
        ], "message" => "Expenses update"]];
        //   LogsDev::finishLog($log, $res, $time, 'D', "Expenses update");
        /////////////////////////////////////////

        // Logs por modulo
        LogsModule::createLog("Expenses", "Update", "admin/expenses/id/edit", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->id);

        return response()->json([
            'expense' => $expense,
            'success' => true,
        ]);
    }

    public function delete(DeleteExpensesRequest $request)
    {

        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", "delete");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        foreach ($request->ids as $id) {
            $expense = Expense::find($id);
            // Logs por modulo
            LogsModule::createLog("Expenses", "delete", "admin/expenses", $expense->id, Auth::user()->name, Auth::user()->email, Auth::user()->role, Auth::user()->company_id, "Expense: ".$expense->id);

        }

        Expense::destroy($request->ids);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "Expenses update"]];
        LogsDev::finishLog($log, $res, $time, 'D', "Expenses update");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    public function setPrefix(Request $request)
    {
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", "setPrefix");
        ///////////////////////////////////////

        $expenses = Expense::all();

        $prefix = $request->expense_prefix;

        if (! empty($prefix) && $request->expenses_prefix_general == 'true') {
            $expenses->each(function ($item) use ($prefix) {
                $up = Expense::find($item['id']);
                $expense_number = explode("-", $up['expense_number']);
                $up->expense_number = $prefix.'-'.$expense_number[1];
                $up->save();
            });
        } else {
            $expenses->each(function ($item) use ($prefix) {
                $up = Expense::find($item['id']);
                if (is_null($up->expense_number)) {
                    $expense_number = explode("-", $up['expense_number']);
                    $up->expense_number = 'EXPE-'.$expense_number[1];
                    $up->save();
                }
            });
        }

        if (empty($prefix) && $request->expenses_prefix_general == 'true') {
            $expenses->each(function ($item) use ($prefix) {
                $up = Expense::find($item['id']);
                $expense_number = explode("-", $up['expense_number']);
                $up->expense_number = 'EXPE-'.$expense_number[1];
                $up->save();
            });
        }

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "setPrefix"]];
        LogsDev::finishLog($log, $res, $time, 'D', "setPrefix");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
        ]);
    }

    // create method for save expenses massive
    public function saveMassiveExpenses(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", "saveBulkExpenses");

        $expenses = $request->listCsv;
        foreach($expenses as $expense) {
            $exist = Expense::where("subject", $expense['subject'])
            ->where("amount", $expense['amount_due'])
            ->where("expense_date", Carbon::parse($expense['date'])->format('Y-m-d'))
            ->where("payment_method_id", $this->getPaymentMethod($expense['payment_method']))
            ->where("expense_category_id", $this->getExpenseCategory($expense['expense_category']))
            ->where("payment_date", Carbon::parse($expense['date'])->format('Y-m-d'))
            ->first();

            if(! $exist) {
                $expenseCreate = new Expense();
                $expenseCreate->subject = $expense['subject'];
                $expenseCreate->creator_id = Auth::user()->id;
                $expenseCreate->expense_number = $this->getNextExpenseNumber();
                $expenseCreate->expense_category_id = $this->getExpenseCategory($expense['expense_category']);
                $expenseCreate->payment_method_id = $this->getPaymentMethod($expense['payment_method']);
                $expenseCreate->expense_date = Carbon::parse($expense['date'])->format('Y-m-d');
                $expenseCreate->status = Carbon::parse($expense['date'])->format('Y-m-d') > Carbon::now() ? 'Pending' : 'Active';
                $expenseCreate->payment_date = Carbon::parse($expense['date'])->format('Y-m-d');
                $expenseCreate->amount = $expense['amount_due'];
                $expenseCreate->company_id = Auth::user()->company_id;
                $expenseCreate->save();
            }
        }

        return response()->json([
            'success' => $request->listCsv,
        ]);

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "saveBulkExpenses"]];
        LogsDev::finishLog($log, $res, $time, 'D', "saveBulkExpenses");
        /////////////////////////////////////////

        return response()->json([
            'success' => 'upload successful',
        ]);
    }

    public function getNextExpenseNumber()
    {
        $lastExpense = Expense::where(
            'company_id',
            Auth::user()->company_id
        )
        ->orderBy('id', 'desc')
        ->first();
        if ($lastExpense) {
            $lastNumber = explode('-', $lastExpense->expense_number);
            $nextNumber = $lastNumber[1] + 1;
            $nextNumber = sprintf('%06d', $nextNumber);

            return  $lastNumber[0].'-'.$nextNumber;
        } else {
            return  'EXPT-'.sprintf('%06d', 1);
        }
    }

    // getExpenseCategory
    public function getExpenseCategory($category)
    {
        $expenseCategory = ExpenseCategory::where('company_id', Auth::user()->company_id)->where('name', "like", "%$category%")->first();
        if ($expenseCategory) {
            return $expenseCategory->id;
        } else {
            $expenseCategory = new ExpenseCategory();
            $expenseCategory->name = $category;
            $expenseCategory->company_id = Auth::user()->company_id;
            $expenseCategory->save();

            return $expenseCategory->id;
        }
    }

    // getPaymentMethod
    public function getPaymentMethod($paymentMethod)
    {
        $paymentMethodobject = PaymentMethod::where('company_id', Auth::user()->company_id)->where('name', $paymentMethod)->first();

        if ($paymentMethodobject) {
            return $paymentMethodobject->id;
        } else {
            $paymentMethodobject = new PaymentMethod();
            $paymentMethodobject->name = $paymentMethod;
            $paymentMethodobject->company_id = Auth::user()->company_id;
            $paymentMethodobject->status = "A";
            $paymentMethodobject->account_accepted = "N";
            $paymentMethodobject->save();

            return $paymentMethodobject->id;
        }
    }

    // getValidPaymentMethods
    public function getValidPaymentMethods(Request $request)
    {
        $time = microtime(true);
        $log = LogsDev::initLog($request, "", "D", "ExpensesController", "getValidPaymentMethods");
        //// Codigo para guardar un log, la parte inicial siempre debe ir al principio del metodo

        $paymentMethods = PaymentMethod::where('company_id', Auth::user()->company_id)->where('expense_import', 1)->get();
        $paymentMethods = $paymentMethods->map(function ($paymentMethod) {
            return [
                'id' => $paymentMethod->id,
                'account_accepted' => $paymentMethod->account_accepted,

            ];
        });

        //  Fin de registro de log, debe guardarse inmediatamente antes de un return,
        $res = ["success" => true, "response" => ["datamesage" => [
            'success' => true,
        ], "message" => "getValidPaymentMethods"]];
        LogsDev::finishLog($log, $res, $time, 'D', "getValidPaymentMethods");
        /////////////////////////////////////////

        return response()->json([
            'success' => true,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function isValidInvoiceAndProvider(Request $request)
    {
        $isValid = \DB::table('expense_invoices')->where('provider_id', '=', $request["provider_id"])
                                                 ->where('invoice_number', '=', $request["invoice_number"])
                                                 ->get();

        return response()->json([
                'success' => true,
                'isValid' => count($isValid) === 0 ? true : false
               ]);
    }
}
