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
        Schema::drop('call_detail_registers');

        Schema::create('call_detail_registers', function (Blueprint $table) {
            $table->id();
            $table->string('from', 60);
            $table->string('to', 60);
            $table->integer('start_date');
            $table->integer('duration');
            $table->integer('billing_duration')->nullable();
            $table->integer('round_duration')->nullable();
            //$table->integer('cost')->nullable();
            $table->float('cost', 9, 5)->unsigned()->nullable();
            $table->string('status', 30); //(8 ⇒ "Answered", 4 ⇒ "Not Answered", 2 ⇒ "Busy", 1 ⇒ "Failed"
            $table->string('unique_id', 25);
            $table->tinyInteger('type')->comment('inbound(0) / outbount(1)')->nullable();
            $table->integer('trunk_id')->nullable();
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
