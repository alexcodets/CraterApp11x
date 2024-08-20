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
        Schema::create('call_detail_register_totals', function (Blueprint $table) {
            $table->id();
            $table->integer('duration');
            $table->integer('total_duration')->comment('The duration applying the minutes increments');
            $table->integer('rate')->nullable();
            $table->integer('calls')->comment('The total calls made');
            $table->float('cost', 9, 5)->unsigned()->nullable();
            $table->tinyInteger('type')->comment('inbound(0) / outbound(1)')->nullable();
            $table->unsignedInteger('pbx_did_id')->nullable();
            $table->unsignedInteger('pbx_extension_id')->nullable();
            $table->foreign('pbx_did_id')->references('id')->on('pbx_did')->onDelete('cascade');
            $table->foreign('pbx_extension_id')->references('id')->on('pbx_extensions')->onDelete('cascade');
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
        Schema::dropIfExists('call_detail_register_totals');
    }
};
