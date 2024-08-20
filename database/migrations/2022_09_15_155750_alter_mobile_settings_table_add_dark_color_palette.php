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
        Schema::table('mobile_settings', function (Blueprint $table) {
            //
            $table->string('dark_color_palette', 255)->after('color_palette');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('mobile_settings', function (Blueprint $table) {
            $table->dropColumn('dark_color_palette');
        });
    }
};
