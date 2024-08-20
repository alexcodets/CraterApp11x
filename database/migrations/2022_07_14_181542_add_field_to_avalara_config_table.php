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
            $table->unsignedBigInteger('services_price_item_id')->nullable()->after('custom_app_rate_item_id');
            $table->unsignedBigInteger('additional_charges_item_id')->nullable()->after('services_price_item_id');
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
            $table->dropColumn('services_price_item_id');
            $table->dropColumn('additional_charges_item_id');
        });
    }
};
