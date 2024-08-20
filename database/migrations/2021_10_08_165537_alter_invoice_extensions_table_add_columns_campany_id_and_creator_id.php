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
        Schema::table('invoice_extensions', function (Blueprint $table) {
            $table->unsignedInteger('company_id')->after('invoice_id');
            $table->unsignedInteger('creator_id')->after('company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoice_extensions', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'creator_id']);
        });
    }
};
