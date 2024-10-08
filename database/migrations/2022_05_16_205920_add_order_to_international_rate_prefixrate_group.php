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
        Schema::table('international_rate_prefixrate_group', function (Blueprint $table) {
            $table->integer('order')->default(0)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('international_rate_prefixrate_group', function (Blueprint $table) {
            $table->dropColumn('order');
        });
    }
};
