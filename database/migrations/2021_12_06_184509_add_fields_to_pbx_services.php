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
        Schema::table('pbx_services', function (Blueprint $table) {
            //
            $table->boolean('allow_pbxpackages')->default(false);
            $table->boolean('allow_items')->default(false);
            $table->boolean('allow_extensions')->default(false);
            $table->boolean('allow_did')->default(false);
            $table->boolean('allow_aditionalcharges')->default(false);
            $table->boolean('allow_usagesummary')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_services', function (Blueprint $table) {
            //
            $table->dropColumn('allow_pbxpackages');
            $table->dropColumn('allow_items');
            $table->dropColumn('allow_extensions');
            $table->dropColumn('allow_did');
            $table->dropColumn('allow_aditionalcharges');
            $table->dropColumn('allow_usagesummary');
        });
    }
};
