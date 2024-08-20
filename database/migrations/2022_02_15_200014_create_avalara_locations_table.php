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
        Schema::create('avalara_locations', function (Blueprint $table) {
            $table->id();
            $table->string('pcd')->nullable()->comment('PCode');
            $table->string('fips')->nullable()->comment('FIPS');
            $table->string('npa')->nullable()->comment('NPANXX');
            $table->json('geo')->nullable()->comment('Geocoded Street Address'); //addr, city, st, zip
            $table->unsignedTinyInteger('type')->nullable()->comment('Loc Type: [0 => pcd, 1 => fips, 2 => npa, 3 => geo]');
            //addr => Street Address, city => City, st => State, zip => Postal Code
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('avalara_locations');
    }
};
