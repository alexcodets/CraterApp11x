<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('aux_vaults', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id', 25);
            $table->decimal('base_amount');
            $table->decimal('amount');
            $table->string('card_number', 4);
            $table->string('email');
            $table->string('address', 250);
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('country', 100)->nullable();
            $table->string('country_code', 6)->nullable();
            $table->string('phone_number', 16);
            $table->string('expiry_date', 4);
            $table->string('cvv', 10)->nullable();
            $table->unsignedSmallInteger('transaction_type')->nullable();
            $table->unsignedBigInteger('user_id');
            //$table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('company_id');
            //$table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aux_vaults');
    }
};
