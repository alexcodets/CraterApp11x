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
            $table->unsignedBigInteger('custom_app_rate_item_id')->nullable()->after('item_international_id');
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
            $table->dropColumn('custom_app_rate_item_id');
        });
    }
};
