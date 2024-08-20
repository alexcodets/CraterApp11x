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
        Schema::table('aux_vaults', function (Blueprint $table) {
            // Verificar si la columna 'phone_number' existe
            if (Schema::hasColumn('aux_vaults', 'phone_number')) {
                // Modificar la columna 'phone_number' para aceptar valores NULL
                $table->string('phone_number')->nullable()->change();
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
        Schema::table('aux_vaults', function (Blueprint $table) {
            //
        });
    }
};
