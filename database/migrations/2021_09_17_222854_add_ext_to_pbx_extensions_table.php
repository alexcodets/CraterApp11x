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
        Schema::table('pbx_extensions', function (Blueprint $table) {
            $table->unsignedInteger('ext')->nullable()->after('ext_name');
            $table->unsignedInteger('pbx_tenant_id')->nullable()->after('tenant_details')->change();
            $table->unsignedInteger('company_id')->nullable()->after('pbx_tenant_id')->change();
            $table->unsignedInteger('creator_id')->nullable()->after('company_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_extensions', function (Blueprint $table) {
            // Aquí puedes revertir los cambios si es necesario
            $table->dropColumn('ext'); // Eliminar la columna 'ext'
            // Puedes agregar aquí la lógica para revertir los otros cambios si es necesario
        });
    }
};
