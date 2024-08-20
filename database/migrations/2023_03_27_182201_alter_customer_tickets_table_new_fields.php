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
        if (Schema::hasTable('customer_tickets')) {
            if (Schema::hasColumn('customer_tickets', 'date') == false) {
                Schema::table('customer_tickets', function (Blueprint $table) {
                    $table->date('date')->after('creator_id');
                });
            }

            if (Schema::hasColumn('customer_tickets', 'time') == false) {
                Schema::table('customer_tickets', function (Blueprint $table) {
                    $table->time('time')->after('date');
                    ;
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
