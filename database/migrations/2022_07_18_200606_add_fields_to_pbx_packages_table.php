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
            $table->boolean('avalara_services_price_item')->default(false)->after('avalara_custom_app_rate_item_id');
            $table->boolean('avalara_additional_charges_item')->default(false)->after('avalara_services_price_item');
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
            $table->dropColumn('avalara_services_price_item');
            $table->dropColumn('avalara_additional_charges_item');
        });
    }
};
