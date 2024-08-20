<?php

use Crater\Models\Company;
use Crater\Models\Expense;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        //

        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        foreach ($companies as $company) {
            $listexpense = Expense::whereNULL("payment_date")->where("company_id", $company->id)->get();
            foreach ($listexpense as $expense) {
                $expense->payment_date = $expense->expense_date;
                $expense->save();
            }
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
