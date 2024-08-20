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
        Schema::create('authorize_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login_id');
            $table->string('transaction_key');
            $table->enum('payment_API', ['CIM', 'AIM']);
            $table->enum('payment_account_validation_mode', ['none', 'test', 'live'])->default('none');
            $table->tinyInteger('test_mode');
            $table->tinyInteger('developer_mode');
            $table->string('currency');
            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('creator_id');
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
        Schema::dropIfExists('authorize_settings');
    }
};
