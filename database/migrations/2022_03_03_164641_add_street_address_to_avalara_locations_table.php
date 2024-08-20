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
        Schema::table('avalara_locations', function (Blueprint $table) {

            $table->boolean('geo')->nullable()->comment('should be geocoded in order to obtain taxing jurisdiction')->change(); //addr, city, st, zip
            //$table->unsignedTinyInteger('type')->nullable()->comment('Loc Type: [0 => pcd, 1 => fips, 2 => npa, 3 => geo, 4 => street]')->change();
            $table->after('id', function ($table) {
                $table->string('county')->nullable();//condado
                $table->string('country')->nullable();
                $table->string('state')->nullable();
                $table->string('city')->nullable();
                $table->text('address')->nullable();
                $table->string('zip')->nullable();
                $table->boolean('incorporated')->nullable();
            });
            $table->integer('user_id')->unsigned()->nullable();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_locations', function (Blueprint $table) {
            //
        });
    }
};
