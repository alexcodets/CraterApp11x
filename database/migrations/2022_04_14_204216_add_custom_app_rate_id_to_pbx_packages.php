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
        Schema::table('pbx_packages', function (Blueprint $table) {
            $table->unsignedBigInteger('custom_app_rate_id')->nullable();
            $table->foreign('custom_app_rate_id')->references('id')->on('custom_app_rates');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_packages', function (Blueprint $table) {
            $table->dropForeign(['custom_app_rate_id']);
            $table->dropColumn('custom_app_rate_id');
        });
    }
};
