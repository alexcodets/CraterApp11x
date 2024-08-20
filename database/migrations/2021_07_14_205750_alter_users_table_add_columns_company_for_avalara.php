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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('company_orf', ['bscl', 'svcl', 'fclt', 'reg'])
                ->after('company_id')
                ->nullable();
            $table->enum('company_orf_type', ["0", "1"])
                ->after('company_orf')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['company_orf', 'company_orf_type']);
        });
    }
};
