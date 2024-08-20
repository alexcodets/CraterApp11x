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
            $table->unsignedSmallInteger('extensionid')->nullable()->after('ext')->comment('Value from pbxSystem, required for the api');
        });

        Schema::table('pbx_did', function (Blueprint $table) {
            $table->unsignedSmallInteger('didid')->nullable()->after('number')->comment('Value from pbxSystem, required for the api');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('did_and_extension', function (Blueprint $table) {
            //
        });
    }
};
