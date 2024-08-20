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
        Schema::create('avalara_locationables', function (Blueprint $table) {
            $table->id();
            $table->string('locationable_type');
            $table->unsignedBigInteger('avalara_location_id');
            $table->unsignedInteger('small_locationable_id')->nullable();
            $table->unsignedBigInteger('locationable_id')->nullable();
            //$table->foreign('avalara_location_id')->references('id')->on('avalara_locations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('avalara_locationables');
    }
};
