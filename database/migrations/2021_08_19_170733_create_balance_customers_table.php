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
        Schema::create('balance_customers', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('status', ['A', 'D']);
            $table->float('present_balance');
            $table->float('amount_op');
            $table->float('amount_final');
            $table->string('transaction_status');
            $table->integer('payment_id');
            $table->integer('user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_customers');
    }
};
