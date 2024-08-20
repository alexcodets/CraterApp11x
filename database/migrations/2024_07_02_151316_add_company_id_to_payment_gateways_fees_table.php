<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('payment_gateways_fees', function (Blueprint $table) {
            // Solo agregar la columna si no existe.
            if (! Schema::hasColumn('payment_gateways_fees', 'company_id')) {
                $table->integer('company_id', false, true)->nullable()->length(10);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {

    }
};
