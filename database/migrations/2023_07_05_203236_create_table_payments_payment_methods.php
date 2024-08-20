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
        if (Schema::hasTable('payments_payment_methods') == false) {
            Schema::create('payments_payment_methods', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('payment_id');
                $table->unsignedInteger('payment_method_id');
                $table->unsignedBigInteger('amount');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payments_payment_methods');
    }
};
