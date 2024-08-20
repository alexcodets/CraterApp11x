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
        Schema::table('addresses', function (Blueprint $table) {
            // $table->foreignId('tax_agency_id')->constrained('tax_agency')->cascadeOnUpdate()->restrictOnDelete()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropForeign('tax_agency_id');
            $table->dropColumn('tax_agency_id');
        });
    }
};
