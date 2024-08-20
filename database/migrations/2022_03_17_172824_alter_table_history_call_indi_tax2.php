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
        //
        Schema::table('history_call_indi', function (Blueprint $table) {
            $table->decimal('percent', 5, 2)->nullable()->default(0);

        });

        Schema::table('call_detail_register_totals_history', function (Blueprint $table) {

            $table->float('taxamount', 9, 5)->unsigned()->nullable()->default(0);
            $table->double('amountbruto', 8, 5)->nullable()->default(0);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
