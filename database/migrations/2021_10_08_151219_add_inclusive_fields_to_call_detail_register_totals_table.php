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
        Schema::table('call_detail_register_totals', function (Blueprint $table) {
            $table->unsignedInteger('exclusive_seconds')->default(0)->after('type');
            $table->float('exclusive_cost', 9, 5)->unsigned()->nullable()->default(0)->after('exclusive_seconds');
            $table->string('number', 100)->nullable()->after('exclusive_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('call_detail_register_totals', function (Blueprint $table) {
            //
        });
    }
};
