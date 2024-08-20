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
        if (Schema::hasTable('payment_gateways_fees')) {
            Schema::table('payment_gateways_fees', function (Blueprint $table) {
                if (Schema::hasColumn('payment_gateways_fees', 'amount')) {
                    $table->bigInteger('amount', false, true)->change();
                }
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
