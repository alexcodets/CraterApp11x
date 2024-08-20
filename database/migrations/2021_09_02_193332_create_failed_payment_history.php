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
        Schema::create('failed_payment_history', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_gateway');
            $table->string('transaction_number')->nullable();
            $table->string('date');
            $table->float('amount');
            $table->string('payment_number');
            $table->integer('customer_id');
            $table->integer('invoice_id')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_payment_history');
    }
};
