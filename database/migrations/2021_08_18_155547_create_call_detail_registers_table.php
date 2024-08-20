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
        Schema::create('call_detail_registers', function (Blueprint $table) {
            $table->id();
            //CDR EndpointData
            $table->string('from', 60);
            $table->string('to', 60);
            $table->timestamp('start_date');
            $table->integer('duration');
            $table->integer('billing_duration');
            $table->integer('cost')->nullable();
            $table->string('status', 30); //(8 ⇒ "Answered", 4 ⇒ "Not Answered", 2 ⇒ "Busy", 1 ⇒ "Failed"
            $table->string('unique_id', 25);

            $table->tinyInteger('type')->comment('to(0) / from(1)')->nullable();
            $table->integer('trunk_id')->nullable();

            //End CDR//////////////////////
            $table->integer('user_id')->unsigned()->nullable();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('call_detail_register');
    }
};
