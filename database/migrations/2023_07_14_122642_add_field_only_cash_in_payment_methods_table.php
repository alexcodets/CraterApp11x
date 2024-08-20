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
            if (Schema::hasColumn('payment_methods', 'only_cash') == false) {
                Schema::table('payment_methods', function (Blueprint $table) {
                    $table->tinyInteger('only_cash')->default(0)->after("status");
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
