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
        Schema::create('payment_gateways_fees', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('type', 255);
            $table->decimal('amount', 8, 2)->default(0);
            $table->enum('payment_gateway', ['Authorize', 'Paypal', 'AuxVault', 'Stripe']);
            $table->unsignedBigInteger('authorize_setting_id')->nullable();
            $table->unsignedBigInteger('aux_vault_setting_id')->nullable();
            $table->unsignedBigInteger('paypal_settings_id')->nullable();
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
