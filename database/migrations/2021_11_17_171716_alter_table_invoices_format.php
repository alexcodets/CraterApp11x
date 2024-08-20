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

        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedBigInteger('pbx_service_price')->default(0)->change();
            ;
            $table->unsignedBigInteger('pbx_total_extension')->default(0)->change();
            ;
            $table->unsignedBigInteger('pbx_total_did')->default(0)->change();
            ;
            $table->unsignedBigInteger('pbx_total_aditional_charges')->default(0)->change();
            ;
            $table->unsignedBigInteger('pbx_total_cdr')->default(0)->change();
            ;
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
