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
        Schema::create('core_pos_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->softDeletes();
            $table->string('document_number')->nullable()->default(null);
            $table->unsignedInteger('creator_id')->nullable()->default(null);
            $table->unsignedInteger('payment_id')->nullable()->default(null);
            $table->unsignedInteger('invoice_id')->nullable()->default(null);
            $table->unsignedInteger('hold_id')->nullable()->default(null);
            $table->string('action')->nullable()->default(null);
            $table->dateTime('date_time')->nullable()->default(null);
            $table->unsignedInteger('item_id')->nullable()->default(null);
            $table->bigInteger('item_price')->nullable()->default(null);
            $table->bigInteger('item_total')->nullable()->default(null);
            $table->bigInteger('item_quantity')->nullable()->default(null);
            $table->string('discount_type')->nullable()->default(null);
            $table->bigInteger('discount_amount')->nullable()->default(null);
            $table->unsignedInteger('tax_id')->nullable()->default(null);
            $table->decimal('tax_type_percent')->nullable()->default(null);
            $table->bigInteger('tax_type_amount')->nullable()->default(null);
            $table->unsignedInteger('customer_id')->nullable()->default(null);
            $table->unsignedInteger('cash_register_id')->nullable()->default(null);
            $table->string('notes')->nullable()->default(null);
            $table->string('tables')->nullable()->default(null);
            $table->bigInteger('qty_persons')->nullable()->default(null);
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
