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
        Schema::table('tax_group', function (Blueprint $table) {
            // Verificar si la clave foránea existe
            if (Schema::hasColumn('tax_group', 'taxes_id')) {
                $table->dropForeign(['taxes_id']); // Eliminar la clave foránea existente
                $table->foreign('taxes_id')->references('id')->on('tax_types')->onDelete('cascade'); // Agregar la nueva clave foránea
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
        Schema::table('tax_group', function (Blueprint $table) {
            $table->dropForeign(['taxes_id']); // Eliminar la clave foránea
        });
    }
};
