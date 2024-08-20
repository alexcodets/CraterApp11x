<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        if (Schema::hasTable('pbx_extension')) {
            if (Schema::hasColumn('pbx_extension', 'dhcp')) {
                \DB::table('pbx_extension')->update(['dhcp' => 1]);
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
        // No se puede revertir el valor de 'dhcp' ya que no se conoce el estado anterior
    }
};
