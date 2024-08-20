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
        Schema::table('corebill_logs_dev', function (Blueprint $table) {
            $table->longText('request')->nullable()->change();
            $table->longText('data_in')->nullable()->change();



            // $table->foreign('pbx_tenant_id')->references('id')->on('pbx_tenant')->onDelete('cascade');
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
