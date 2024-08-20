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
            //
            // borrar campos json
            $table->dropColumn(['ext_details']);
            // rename columns
            $table->renameColumn('ext_name', 'name');
            $table->renameColumn('ext_email', 'email');
            $table->renameColumn('ext_status', 'status');
            //
            $table->string('ua_name')->nullable()->after('pbx_tenant_id');
            $table->string('ua_id')->nullable()->after('pbx_tenant_id');
            $table->string('ua_fullname')->nullable()->after('pbx_tenant_id');
            $table->string('protocol')->nullable()->after('pbx_tenant_id');
            $table->string('macaddress')->nullable()->after('pbx_tenant_id');
            $table->string('location')->nullable()->after('pbx_tenant_id');
            $table->string('linenum')->nullable()->after('pbx_tenant_id');

            $table->string('api_id')->nullable()->after('pbx_tenant_id');
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
            //
        });
    }
};
