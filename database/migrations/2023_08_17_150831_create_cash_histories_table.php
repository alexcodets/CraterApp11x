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
        Schema::create('cash_histories', function (Blueprint $table) {
            $table->id();
            $table->string('ref')->unique();
            $table->unsignedBigInteger('cash_register_id');
            $table->decimal('cash_received')->default(0);
            $table->decimal('initial_amount')->default(0);
            $table->decimal('final_amount')->default(0)->nullable();
            $table->boolean('open')->default(1);
            $table->string('open_note')->default(null)->nullable();
            $table->string('close_note')->default(null)->nullable();
            $table->timestamp('open_date')->default(null)->nullable();
            $table->timestamp('close_date')->default(null)->nullable();
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
        Schema::dropIfExists('cash_histories');
    }
};
