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
        Schema::table('international_rate', function (Blueprint $table) {
            $table->unsignedInteger('prefixrate_groups_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('international_rate', function (Blueprint $table) {
            $table->unsignedInteger('prefixrate_groups_id')->change();
        });
    }
};
