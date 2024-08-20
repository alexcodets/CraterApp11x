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

        Schema::table('payment_methods', function (Blueprint $table) {
            // Verificar si el campo 'add_payment_gateway' existe
            if (! Schema::hasColumn('payment_methods', 'add_payment_gateway')) {
                // Si no existe, crearlo como tinyInteger con default 0
                $table->tinyInteger('add_payment_gateway')->default(0);
            }
        });

        // Actualizar los registros existentes donde 'add_payment_gateway' sea null a 0
        DB::table('payment_methods')
            ->whereNull('add_payment_gateway')
            ->update(['add_payment_gateway' => 0]);
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
