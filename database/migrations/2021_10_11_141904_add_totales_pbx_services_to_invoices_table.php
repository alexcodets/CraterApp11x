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
        Schema::table('invoices', function (Blueprint $table) {
            $table->decimal('pbx_total_items', 8, 2)->default(0);
            $table->decimal('pbx_total_extension', 8, 2)->default(0);
            $table->decimal('pbx_total_did', 8, 2)->default(0);
            $table->decimal('pbx_total_aditional_charges', 8, 2)->default(0);
            $table->decimal('pbx_total_cdr', 8, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('pbx_total_items');
            $table->dropColumn('pbx_total_extension');
            $table->dropColumn('pbx_total_did');
            $table->dropColumn('pbx_total_aditional_charges');
            $table->dropColumn('pbx_total_cdr');
        });
    }
};
