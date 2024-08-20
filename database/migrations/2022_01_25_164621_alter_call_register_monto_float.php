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
        Schema::table('call_detail_register_totals_history', function (Blueprint $table) {
            $table->float('amount', 9, 5)->unsigned()->default(0)->change();



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
