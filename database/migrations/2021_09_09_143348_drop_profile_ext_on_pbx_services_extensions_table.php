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
        Schema::table('pbx_services_extensions', function (Blueprint $table) {
            // $table->dropForeign('pbx_services_extensions_pbx_profile_extension_id_foreign');
            $table->dropColumn('pbx_profile_extension_id');
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_services_extensions', function (Blueprint $table) {
            //
        });
    }
};
