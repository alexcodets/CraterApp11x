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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email');
            $table->string('suffix');
            $table->string('company')->nullable();
            $table->string('display_name');
            $table->tinyInteger('display_name_check')->nullable();
            $table->integer('country_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->integer('zip_code')->nullable();
            $table->string('description')->nullable();
            $table->integer('phone')->nullable();
            $table->integer('mobile')->nullable();
            $table->string('fax')->nullable();
            $table->string('other')->nullable();
            $table->string('webside')->nullable();
            $table->string('terms')->nullable();
            $table->integer('opening_balance')->nullable();
            $table->date('as_of')->nullable();
            $table->integer('account_no')->nullable();
            $table->integer('business_no')->nullable();
            $table->tinyInteger('track_payments')->nullable();
            $table->string('default_expense_account')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('creator_id')->nullable();
            $table->enum('status', ['A', 'T']);
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
        Schema::dropIfExists('providers');
    }
};
