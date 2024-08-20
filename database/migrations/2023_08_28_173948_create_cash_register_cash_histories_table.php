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
        Schema::create('cash_register_cash_histories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->bigInteger('amount')->default(0);
            $table->enum('type', ['I', 'R']);
            $table->unsignedBigInteger('cash_histories_id');
            $table->unsignedBigInteger('cash_register_id');
            $table->unsignedBigInteger('creator_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_register_cash_histories');
    }
};
