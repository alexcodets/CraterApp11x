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
        Schema::table('authorize', function (Blueprint $table) {
            // despues de card_number
            $table->string('credit_card_full')->nullable()->after('card_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('authorize', function (Blueprint $table) {
            $table->dropColumn('credit_card_full');
        });
    }
};
