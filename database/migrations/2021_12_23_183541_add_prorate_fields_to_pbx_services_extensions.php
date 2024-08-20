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

        Schema::table('pbx_extensions', function (Blueprint $table) {
            //
            $table->bigInteger('cost_per_day')->nullable();
            $table->bigInteger('prorate')->nullable();
            $table->date('date_prorate')->nullable();
        });
        Schema::table('pbx_services_extensions', function (Blueprint $table) {
            //
            $table->bigInteger('cost_per_day')->nullable();
            $table->bigInteger('prorate')->nullable();
            $table->date('date_prorate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_services_extensions', function (Blueprint $table) {
            //
            $table->dropColumn('cost_per_day');
            $table->dropColumn('prorate');
            $table->dropColumn('date_prorate');
        });
    }
};
