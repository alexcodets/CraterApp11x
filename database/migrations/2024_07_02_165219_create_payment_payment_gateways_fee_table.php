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
        Schema::create('payment_payment_gateways_fee', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->unsignedBigInteger('payment_gateways_fee_id');
            $table->string('name');
            $table->string('type');
            // amount es el monto de la tabla payment gateways fees
            $table->decimal('amount', 10, 2)->comment('amount es el monto de la tabla payment gateways fees');
            // total es lo que representa el amount respecto al payment
            $table->unsignedBigInteger('total')->comment('total es lo que representa el amount respecto al payment');
            $table->integer('company_id', false, true)->nullable()->length(10);

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

    }
};
