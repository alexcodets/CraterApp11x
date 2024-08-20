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
        Schema::create('invoice_late_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('amount');
            $table->unsignedTinyInteger('type');
            $table->string('notice')->nullable();
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('tax_amount');
            $table->unsignedBigInteger('total');
            $table->unsignedInteger('invoice_id');
            $table->timestamps();
            $table->softDeletes();
            /*$table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_late_fees');
    }
};
