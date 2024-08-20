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
        //Schema::drop('call_detail_registers');
        Schema::table('call_detail_register_totals', function (Blueprint $table) {
            $table->unsignedInteger('pbx_services_id')->nullable()->after('pbx_extension_id');
            $table->foreign('pbx_services_id')->references('id')->on('pbx_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('call_detail_register_totals', function (Blueprint $table) {
            //
        });
    }
};
