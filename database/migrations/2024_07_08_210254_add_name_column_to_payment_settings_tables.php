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
        // Lista de tablas a verificar
        $tables = [
            'authorize_settings',
            'aux_vault_settings',
            'paypal_settings',
        ];

        foreach ($tables as $table) {
            // Verificar si la tabla existe
            if (Schema::hasTable($table)) {
                // Verificar si el campo 'name' existe
                if (! Schema::hasColumn($table, 'name')) {
                    Schema::table($table, function (Blueprint $table) {
                        // Añadir el campo 'name' que acepte null
                        $table->string('name', 255)->nullable();
                    });
                }
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
