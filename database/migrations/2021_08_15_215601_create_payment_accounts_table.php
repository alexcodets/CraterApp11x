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
        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->integer('country_id');
            $table->integer('state_id');
            $table->string('city');
            $table->string('address_1');
            $table->string('address_2');
            $table->integer('zip');
            $table->enum('payment_account_type', ['CC', 'ACH']);
            $table->string('card_number')->nullable();
            $table->string('cvv')->nullable();
            $table->string('expiration_date')->nullable();
            $table->string('ACH_type')->nullable();
            $table->string('account_number')->nullable();
            $table->string('routing_number')->nullable();
            $table->integer('client_id');
            $table->integer('company_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_accounts');
    }
};
