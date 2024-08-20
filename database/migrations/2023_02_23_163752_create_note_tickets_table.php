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
        Schema::create('note_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('subject');
            $table->string('message');
            // $table->foreignId('user_id')->constrained();
            //$table->foreignId('customer_ticket_id')->constrained();
            $table->unsignedBigInteger('customer_ticket_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('note_tickets');
    }
};
