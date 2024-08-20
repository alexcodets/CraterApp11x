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
            $table->unsignedInteger('inter_custom_destinations_item_id')->after('custom_destinations_item_id')->nullable();
            $table->unsignedInteger('toll_free_custom_destinations_item_id')->after('inter_custom_destinations_item_id')->nullable();
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
            $table->dropColumn('inter_custom_destinations_item_id');
            $table->dropColumn('toll_free_custom_destinations_item_id');
        });
    }
};
