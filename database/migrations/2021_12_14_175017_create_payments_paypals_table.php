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
        Schema::create('payments_paypals', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->string('email_address')->nullable();
            $table->float('amount', 10, 2)->nullable();
            $table->string('currency')->nullable();
            $table->string('country_code')->nullable();
            $table->string('payment_status')->nullable();
            $table->integer('payment_id')->nullable();
            $table->string('create_time')->nullable();
            $table->integer('creator_id')->nullable();
            $table->integer('company_id')->nullable();
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
        Schema::dropIfExists('payments_paypals');
    }
};
