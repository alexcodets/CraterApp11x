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
        if (Schema::hasTable('hold_taxes')) {
            Schema::dropIfExists('hold_taxes');
        }
        Schema::create('hold_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hold_invoice_id');
            $table->unsignedBigInteger('tax_type_id');
            $table->unsignedBigInteger('amount');
            $table->tinyInteger('compound_tax')->default(0);
            $table->decimal('percent');
            $table->string('name');
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
        Schema::dropIfExists('hold_taxes');
    }
};
