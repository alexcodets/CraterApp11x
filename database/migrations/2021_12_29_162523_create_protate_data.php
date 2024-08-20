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
        if (Schema::hasColumn('pbx_services_did', 'cost_per_day') == false) {
            Schema::table('pbx_services_did', function (Blueprint $table) {
                $table->bigInteger('cost_per_day')->nullable();
            });
        }

        if (Schema::hasColumn('pbx_services_did', 'prorate') == false) {
            Schema::table('pbx_services_did', function (Blueprint $table) {
                $table->bigInteger('prorate')->nullable();
            });
        }

        if (Schema::hasColumn('pbx_services_did', 'date_prorate') == false) {
            Schema::table('pbx_services_did', function (Blueprint $table) {
                $table->date('date_prorate')->nullable();
            });
        }

        if (Schema::hasColumn('pbx_services_extensions', 'cost_per_day') == false) {
            Schema::table('pbx_services_extensions', function (Blueprint $table) {
                $table->bigInteger('cost_per_day')->nullable();
            });
        }

        if (Schema::hasColumn('pbx_services_extensions', 'prorate') == false) {
            Schema::table('pbx_services_extensions', function (Blueprint $table) {
                $table->bigInteger('prorate')->nullable();
            });
        }

        if (Schema::hasColumn('pbx_services_extensions', 'date_prorate') == false) {
            Schema::table('pbx_services_extensions', function (Blueprint $table) {
                $table->date('date_prorate')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {

    }
};
