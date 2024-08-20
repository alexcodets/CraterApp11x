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
        if (! Schema::hasColumn('note_tickets', 'public')) {
            Schema::table('note_tickets', function (Blueprint $table) {
                $table->tinyInteger('public')->nullable();
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
        Schema::table('note_tickets', function (Blueprint $table) {
            //
        });
    }
};
