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
        Schema::table('hold_invoices', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('hold_items', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('hold_contacts', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('hold_taxes', function (Blueprint $table) {
            $table->softDeletes();
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
