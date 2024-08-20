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
        Schema::table('balance_customers', function (Blueprint $table) {
            $table->float('present_balance', 9, 5)->unsigned()->nullable()->default(0)->change();
            $table->float('amount_op', 9, 5)->unsigned()->nullable()->default(0)->change();
            $table->float('amount_final', 9, 5)->unsigned()->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('balance_customers', function (Blueprint $table) {
            //
        });
    }
};
