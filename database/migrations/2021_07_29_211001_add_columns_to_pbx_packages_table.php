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
        Schema::table('pbx_packages', function (Blueprint $table) {
            //
            $table->float('rate', 8, 2)->default(0);
            $table->string('national_dialing_code', 50)->nullable();
            $table->string('international_dialing_code', 50)->nullable();
            $table->integer('inclusive_minutes')->default(0)->nullable();
            $table->integer('international_inclusive_minutes')->default(0)->nullable();
            $table->integer('local_inclusive_minutes')->default(0)->nullable();



        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_packages', function (Blueprint $table) {
            //
        });
    }
};
