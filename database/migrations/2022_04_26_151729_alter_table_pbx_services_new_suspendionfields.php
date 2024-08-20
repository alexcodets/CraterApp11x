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
        //

        Schema::table('pbx_services', function (Blueprint $table) {
            $table->enum('suspension_type', ['T', 'E'])->default('T');
            $table->unsignedBigInteger('custom_app_rate_id')->nullable();
            $table->foreign('custom_app_rate_id')->references('id')->on('custom_app_rates');
            $table->boolean('allow_customapp')->default(false);
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
