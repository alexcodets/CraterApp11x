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
            $table->unsignedInteger('start_date');
            $table->unsignedInteger('duration');
            $table->unsignedInteger('billing_duration')->nullable();
            $table->unsignedInteger('round_duration')->nullable();
            $table->float('cost', 9, 5)->unsigned()->nullable();
            $table->string('status', 30); //(8 ⇒ "Answered", 4 ⇒ "Not Answered", 2 ⇒ "Busy", 1 ⇒ "Failed"
            $table->string('unique_id', 25);
            $table->tinyInteger('type')->comment('inbound(0) / outbound(1)')->nullable();
            $table->integer('trunk_id')->nullable();
            $table->date('billed_at')->nullable();
            $table->unsignedInteger('pbx_did_id')->nullable();
            $table->unsignedInteger('pbx_extension_id')->nullable();
            $table->foreign('pbx_did_id')->references('id')->on('pbx_did')->onDelete('cascade');
            $table->foreign('pbx_extension_id')->references('id')->on('pbx_extensions')->onDelete('cascade');
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
