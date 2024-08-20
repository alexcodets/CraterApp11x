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
        if (Schema::hasTable('note_tickets')) {
            if (Schema::hasColumn('note_tickets', 'date') == false) {
                Schema::table('note_tickets', function (Blueprint $table) {
                    $table->date('date')->after('user_id');
                });
            }

            if (Schema::hasColumn('note_tickets', 'time') == false) {
                Schema::table('note_tickets', function (Blueprint $table) {
                    $table->time("time")->after('date');
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
