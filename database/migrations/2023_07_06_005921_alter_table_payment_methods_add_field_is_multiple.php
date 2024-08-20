<?php

use Carbon\Carbon;
use Crater\Models\PaymentMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('payment_methods')) {
            if (! Schema::hasColumn('payment_methods', 'is_multiple')) {
                Schema::table('payment_methods', function (Blueprint $table) {
                    $table->boolean('is_multiple')->default(0)->nullable();
                });
            }

            $exist = PaymentMethod::where("name", "Multiple")->where("is_multiple", 1)->first();

            if(! $exist && \Crater\Models\Company::where('id', 1)->exists()) {
                \DB::table('payment_methods')->insert(
                    [
                        'name' => "Multiple",
                        'status' => "A",
                        'add_payment_gateway' => 0,
                        'paypal_button' => 0,
                        'company_id' => 1,
                        'account_accepted' => "N",
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                        'deleted_at' => null,
                        'payment_gateways_id' => null,
                        'for_customer_use' => 0,
                        'generate_expense' => 0,
                        'void_refund' => 0,
                        'generate_expense_id' => null,
                        'void_refund_expense_id' => null,
                        'expense_import' => 0,
                        'is_multiple' => 1,
                    ]
                );
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
