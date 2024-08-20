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
        if (Schema::hasTable('hold_items')) {
            Schema::dropIfExists('hold_items');
        }

        Schema::create('hold_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->bigInteger('price');
            $table->decimal('quantity');
            $table->string('unit_name')->nullable();
            $table->decimal('discount');
            $table->bigInteger('discount_val');
            $table->bigInteger('tax');
            $table->bigInteger('total');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('retentions_id')->nullable();
            $table->string('retention_concept')->nullable();
            $table->decimal('retention_percentage')->nullable();
            $table->bigInteger('retention_amount')->nullable();
            $table->unsignedBigInteger('hold_invoice_id');
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
        Schema::dropIfExists('hold_items');
    }
};
