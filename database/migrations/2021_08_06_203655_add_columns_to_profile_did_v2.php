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
        Schema::table('profile_did', function (Blueprint $table) {
            //
            $table->string('inbound_per_minute_rate_value', 250)->nullable();
            $table->string('emergency_services_rate_value', 250)->nullable();
            $table->string('emergency_services_city', 250)->nullable();
            $table->string('emergency_services_state', 250)->nullable();
            $table->string('emergency_services_zip', 250)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('profile_did', function (Blueprint $table) {
            //
        });
    }
};
