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
            $table->dropColumn('cc_number');
            $table->dropColumn('expiry_month');
            $table->dropColumn('expiry_year');
            $table->dropColumn('cvv');
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
