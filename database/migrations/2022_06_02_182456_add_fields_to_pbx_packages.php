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
            $table->unsignedBigInteger('avalara_custom_app_rate_item_id')->nullable()->after('custom_app_rate_id');
            $table->unsignedBigInteger('avalara_extension_item_id')->nullable()->after('custom_app_rate_id');
            $table->unsignedBigInteger('avalara_did_item_id')->nullable()->after('custom_app_rate_id');
            $table->unsignedBigInteger('cdr_items_id')->nullable()->after('custom_app_rate_id');
            $table->unsignedBigInteger('custom_destinations_item_id')->nullable()->after('custom_app_rate_id');
            $table->boolean('avalara_custom_app_rate_items')->default(false)->after('avalara_callrating');
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
            $table->dropColumn('avalara_custom_app_rate_item_id');
            $table->dropColumn('avalara_extension_item_id');
            $table->dropColumn('avalara_did_item_id');
            $table->dropColumn('cdr_items_id');
            $table->dropColumn('custom_destinations_item_id');
            $table->dropColumn('avalara_custom_app_rate_items');
        });
    }
};
