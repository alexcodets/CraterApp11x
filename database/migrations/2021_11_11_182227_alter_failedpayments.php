<?php

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
        //
        Schema::table('failed_payment_history', function (Blueprint $table) {

            $table->string('payment_gateway')->nullable()->change();
            $table->string('transaction_number')->nullable()->change();
            $table->string('date')->nullable()->change();
            $table->float('amount')->nullable()->change();
            $table->string('payment_number')->nullable()->change();
            $table->integer('customer_id')->nullable()->change();
            $table->integer('invoice_id')->nullable()->change();
            $table->string('description')->nullable()->change();

        });
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
