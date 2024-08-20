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
        //

        Schema::table('authorize', function (Blueprint $table) {
            $table->string('transaction_id')->nullable()->change();
            $table->string('payer_email')->nullable()->change();
            $table->float('amount', 10, 2)->nullable()->change();
            $table->string('currency')->nullable()->change();
            $table->string('payment_status')->nullable()->change();
            $table->integer('payment_id')->nullable()->change();
            $table->integer('creator_id')->nullable()->change();
            $table->integer('company_id')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('zip')->nullable()->change();
            $table->string('country')->nullable()->change();
            $table->integer('country_id')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->integer('state_id')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('address_street_2')->nullable()->change();
            $table->string('address_street_1')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('expiration_date')->nullable()->change();
            $table->string('ACH_type')->nullable()->change();
            $table->string('account_number')->nullable()->change();
            $table->string('bank_name')->nullable()->change();
            $table->string('name_on_account')->nullable()->change();
            $table->string('num_check')->nullable()->change();
            $table->string('routing_number')->nullable()->change();
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
