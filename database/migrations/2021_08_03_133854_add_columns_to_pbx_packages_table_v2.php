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
            $table->enum('status_payment', ['prepaid', 'postpaid']);
            $table->enum('type_time_increment', ['sec', 'min']);
            $table->integer('minutes_increments')->default(0)->nullable();
            $table->integer('rate_per_minutes')->default(0)->nullable();
            $table->float('value_discount', 8, 2)->default(0)->nullable();
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
