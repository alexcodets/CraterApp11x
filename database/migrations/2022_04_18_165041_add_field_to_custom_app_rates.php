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
        Schema::table('custom_app_rates', function (Blueprint $table) {
            $table->decimal('office_price', 8, 2)->default(0)->after('office');
            $table->decimal('bussiness_price', 8, 2)->default(0)->after('bussiness');
            $table->decimal('web_price', 8, 2)->default(0)->after('web');
            $table->decimal('agent_price', 8, 2)->default(0)->after('agent');
            $table->decimal('supervisor_price', 8, 2)->default(0)->after('supervisor');
            $table->decimal('mobile_price', 8, 2)->default(0)->after('mobile');
            $table->decimal('crm_price', 8, 2)->default(0)->after('crm');
            $table->decimal('call_pop_up_price', 8, 2)->default(0)->after('call_pop_up');

            // delete column price
            $table->dropColumn('price');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('custom_app_rates', function (Blueprint $table) {
            $table->dropColumn('office_price');
            $table->dropColumn('bussiness_price');
            $table->dropColumn('web_price');
            $table->dropColumn('agent_price');
            $table->dropColumn('supervisor_price');
            $table->dropColumn('mobile_price');
            $table->dropColumn('crm_price');
            $table->dropColumn('call_pop_up_price');
        });
    }
};
