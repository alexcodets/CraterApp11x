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
            // quitar nullable de minutes_increments y rate_per_minutes
            $table->integer('minutes_increments')->default(1)->nullable(false)->change();
            // $table->integer('rate_per_minutes')->default(0)->nullable(false)->change();
            $table->float('value_discount', 8, 2)->default(0)->nullable(false)->change();
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
