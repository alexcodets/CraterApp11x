<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('transaction_fees', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 50)->nullable();
            $table->string('name', 50);
            $table->string('type', 15)->default('Fixed');//fixed, percentage
            $table->unsignedBigInteger('amount')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->unsignedBigInteger('payment_gateways_fee_id')->nullable();
            //$table->foreign('payment_gateways_fee_id')->references('id')->on('payment_gateways_fees')->onDelete('cascade');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedBigInteger('authorize_id')->nullable();
            $table->unsignedBigInteger('aux_vault_id')->nullable();
            $table->unsignedBigInteger('payments_paypal_id')->nullable();
            $table->softDeletes();

            $table->timestamps();
        });
    }
};
