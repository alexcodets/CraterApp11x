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
        if (Schema::hasTable('payments_payment_methods')) {
            if (Schema::hasColumn('payments_payment_methods', 'received') == false) {
                Schema::table('payments_payment_methods', function (Blueprint $table) {
                    $table->unsignedBigInteger('received')->after('amount');
                });
            }

            if (Schema::hasColumn('payments_payment_methods', 'returned') == false) {
                Schema::table('payments_payment_methods', function (Blueprint $table) {
                    $table->unsignedBigInteger('returned')->after('received');
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
