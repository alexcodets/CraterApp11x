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
        // Lista de tablas a verificar y actualizar
        $tables = [
            'authorize_settings',
            'aux_vault_settings',
            'paypal_settings',
        ];

        foreach ($tables as $tableName) {
            // Verificar si la tabla existe
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    // Agregar campos si no existen
                    if (! Schema::hasColumn($tableName, 'enable_identification_verification')) {
                        $table->tinyInteger('enable_identification_verification')->nullable()->default(0);
                    }
                    if (! Schema::hasColumn($tableName, 'enable_fee_charges')) {
                        $table->tinyInteger('enable_fee_charges')->nullable()->default(0);
                    }
                });
            }
        }
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
