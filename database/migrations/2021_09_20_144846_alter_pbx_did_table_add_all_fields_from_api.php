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

        Schema::table('pbx_did', function (Blueprint $table) {
            // borrar campos json
            $table->dropColumn(['did_details']);
            // rename columns
            $table->renameColumn('did_number', 'number');
            $table->renameColumn('did_server', 'server');
            $table->renameColumn('did_status', 'status');
            //
            $table->string('api_id')->nullable()->after('pbx_tenant_id');
            $table->string('e164')->nullable()->after('pbx_tenant_id');
            $table->string('e164_2')->nullable()->after('pbx_tenant_id');
            $table->string('ext')->nullable()->after('pbx_tenant_id');
            $table->string('number2')->nullable()->after('pbx_tenant_id');
            $table->string('type')->nullable()->after('pbx_tenant_id');
            $table->string('trunk')->nullable()->after('pbx_tenant_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_did', function (Blueprint $table) {
            //
        });
    }
};
