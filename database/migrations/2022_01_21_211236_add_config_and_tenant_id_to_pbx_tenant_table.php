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
        Schema::table('pbx_tenant', function (Blueprint $table) {
            $table->unsignedSmallInteger('tenantid')->nullable()->after('code')->comment('Value from pbxSystem, required for the api');
            $table->json('config')->nullable()->after('tenantid');
            $table->unsignedTinyInteger('status')->default(1)->after('config');
            //id, config
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_tenant', function (Blueprint $table) {
            $table->dropColumn('tenantid');
            $table->dropColumn('config');
            $table->dropColumn('status');
        });
    }
};
