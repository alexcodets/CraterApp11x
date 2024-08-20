<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        // Lista de tablas a las que se les añadirán los campos
        $tables = [
            'customer_package_items',
            'pbx_services_items',
            'package_items',
            'pbx_package_items',
        ];

        foreach ($tables as $table) {
            // Verificar si la tabla existe
            if (Schema::hasTable($table)) {
                Schema::table($table, function (Blueprint $table) {
                    // Añadir campo boolean 'end_period_act' con valor por defecto false
                    $table->boolean('end_period_act')->default(false);
                    // Añadir campo entero 'end_period_number' con valor por defecto 1
                    $table->integer('end_period_number')->default(1);
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
