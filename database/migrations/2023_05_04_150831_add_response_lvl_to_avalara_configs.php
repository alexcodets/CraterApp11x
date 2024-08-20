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

            $table->after('lfln', function (Blueprint $table) {
                // Response lvl
                $table->boolean('dtl')->default(false)->comment('Return/Response Detail');
                $table->boolean('summ')->default(false)->comment('Return/Response Summary');
                //Request Config
                $table->boolean('retnb')->nullable()->comment('Return Non-Billable Taxes');
                $table->boolean('retext')->nullable()->comment('Return Extended Data');
                $table->boolean('incrf')->nullable()->comment('Return Reporting Information');
            });

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
            //
        });
    }
};
