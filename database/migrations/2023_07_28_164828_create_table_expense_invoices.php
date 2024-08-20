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
        Schema::create('expense_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('expense_id');
            $table->unsignedInteger('provider_id');
            $table->string('invoice_number');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('total_tax');
            $table->unsignedBigInteger('total');
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
        Schema::dropIfExists('expense_invoices');
    }
};
