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
        //

        Schema::table('tax_types', function (Blueprint $table) {

            $table->unsignedBigInteger('tax_agency_id')->nullable();



        });

        Schema::table('tax_groups', function (Blueprint $table) {

            $table->unsignedBigInteger('tax_agency_id')->nullable();



        });

        Schema::table('addresses', function (Blueprint $table) {

            $table->unsignedBigInteger('tax_agency_id')->nullable();



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
