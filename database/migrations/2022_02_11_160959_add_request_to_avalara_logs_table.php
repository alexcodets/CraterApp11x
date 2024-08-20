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
        Schema::table('avalara_logs', function (Blueprint $table) {
            $table->json('request')->nullable()->after('status')->comment('data sent to the endpoint');
            $table->unsignedSmallInteger('procesing_time')->nullable()->after('response')->comment('procesing time in miliseconds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_logs', function (Blueprint $table) {
            $table->dropColumn('request');
            $table->dropColumn('procesing_time');
        });
    }
};
