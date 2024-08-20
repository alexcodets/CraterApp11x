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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('avalara_bool')
                ->after('no_taxable')
                ->nullable();
            $table->enum('avalara_type', ['19', '20', '59', '65'])
                ->after('avalara_bool')
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
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['avalara_bool', 'avalara_type']);
        });
    }
};
