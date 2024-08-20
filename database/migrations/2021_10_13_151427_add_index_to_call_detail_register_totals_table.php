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
            $table->index(['pbx_extension_id', 'invoice_id', 'number'], 'cdr_totals_ext_inv_numb');
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
