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
        Schema::create('paypal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('paypal_id');
            $table->string('paypal_secret');
            $table->string('paypal_signature');
            $table->string('currency');
            $table->enum('status', ['A', 'I'])->default('A');
            $table->tinyInteger('test_mode');
            $table->tinyInteger('developer_mode');
            $table->unsignedInteger('creator_id')->default(0);
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
        Schema::dropIfExists('paypal_settings');
    }
};
