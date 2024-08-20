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
        // Verifica si la columna 'dhcp' existe y cambia el valor predeterminado
        if (Schema::hasColumn('pbx_extensions', 'dhcp')) {
            Schema::table('pbx_extensions', function (Blueprint $table) {
                $table->integer('dhcp')->default(1)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_extensions', function (Blueprint $table) {
            //
        });
    }
};
