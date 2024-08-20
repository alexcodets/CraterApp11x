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
            $table->unsignedBigInteger('avalara_services_price_item_id')->nullable()->after('avalara_custom_app_rate_item_id');
            $table->unsignedBigInteger('avalara_additional_charges_item_id')->nullable()->after('avalara_services_price_item_id');
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
            $table->dropColumn('avalara_services_price_item_id');
            $table->dropColumn('avalara_additional_charges_item_id');
        });
    }
};
