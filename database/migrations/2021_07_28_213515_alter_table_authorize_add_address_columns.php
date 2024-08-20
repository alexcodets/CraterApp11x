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
        Schema::table('authorize', function (Blueprint $table) {
            $table->string('phone')->after('cvv');
            $table->string('zip')->after('cvv');
            $table->string('country')->after('cvv');
            $table->integer('country_id')->after('cvv');
            $table->string('state')->after('cvv');
            $table->integer('state_id')->after('cvv');
            $table->string('city')->after('cvv');
            $table->string('address_street_2')->after('cvv');
            $table->string('address_street_1')->after('cvv');
            $table->string('name')->nullable()->after('cvv');
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
