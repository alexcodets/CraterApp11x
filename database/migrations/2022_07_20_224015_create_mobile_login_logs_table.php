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
        Schema::create('mobile_login_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamp('session_start')->nullable();
            $table->string('firebase_code', 255)->nullable();
            $table->string('system_name', 255)->nullable();
            $table->string('system_version', 255)->nullable();
            $table->string('device_name', 255)->nullable();
            $table->boolean('is_tablet', 255)->nullable();
            $table->string('serial_number', 255)->nullable();
            $table->string('brand', 255)->nullable();
            $table->string('device_id', 255)->nullable();
            $table->string('device_type', 255)->nullable();
            $table->string('unique_id', 255)->nullable();
            $table->string('manufacturer', 255)->nullable();
            $table->string('api_level', 255)->nullable();
            $table->string('mac_address', 255)->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('mobile_login_logs');
    }
};
