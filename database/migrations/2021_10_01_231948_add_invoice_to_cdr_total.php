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
        Schema::table('call_detail_register_totals', function (Blueprint $table) {
            $table->float('cost', 12, 5)->unsigned()->nullable()->change();
            $table->unsignedInteger('invoice_id')->nullable()->after('pbx_extension_id');
            // $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
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
