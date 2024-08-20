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
        Schema::create('table_payment_devolutions', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id')->nullable();
            $table->unsignedInteger('transaction_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('payload')->nullable();
            $table->string('status')->nullable();
            $table->unsignedBigInteger('amount');
            $table->date('date');
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
        Schema::dropIfExists('table_payment_devolutions');
    }
};
