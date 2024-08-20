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
        Schema::table('avalara_configs', function (Blueprint $table) {
            $table->unsignedInteger('item_custom_id')->after('item_international_id')->nullable();
            $table->unsignedInteger('item_toll_free_id')->after('item_custom_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_configs', function (Blueprint $table) {
            $table->dropColumn('item_custom_id');
            $table->dropColumn('item_toll_free_id');
        });
    }
};
