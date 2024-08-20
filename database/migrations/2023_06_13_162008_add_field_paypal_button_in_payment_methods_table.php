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
        if (Schema::hasTable('payment_methods')) {
            if (Schema::hasColumn('payment_methods', 'paypal_button') == false) {
                Schema::table('payment_methods', function (Blueprint $table) {
                    $table->tinyInteger('paypal_button')->default(0)->after("add_payment_gateway");
                });
            }
        }
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
