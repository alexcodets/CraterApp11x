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
        Schema::create('customer_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('summary');
            $table->text('note')->nullable();
            $table->integer('dep_id')->unsigned();
            $table->integer('assigned_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->enum('priority', ['E', 'C','H','M','L'])->default('H');
            $table->enum('status', ['S', 'C','I','O','M'])->default('S');
            $table->integer('company_id')->unsigned();
            $table->integer('creator_id')->unsigned();
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
        Schema::dropIfExists('customer_tickets');
    }
};
