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

        if (Schema::hasTable('hold_invoices')) {
            Schema::dropIfExists('hold_invoices');
        }
        Schema::create('hold_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->date('invoice_date');
            $table->date('due_date');
            $table->unsignedBigInteger('user_id');
            $table->bigInteger('total');
            $table->bigInteger('due_amount');
            $table->bigInteger('sub_total');
            $table->bigInteger('tax');
            $table->string('discount_type');
            $table->decimal('discount');
            $table->bigInteger('discount_val');
            $table->unsignedBigInteger('cash_register_id');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('company_id');
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
        Schema::dropIfExists('hold_invoices');
    }
};
