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
        Schema::table('packages', function (Blueprint $table) {
            $table->enum('apply_tax_type', ['none', 'item', 'general'])->default('general')->nullable()
            ->after('html');
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn(['apply_tax_type']);
        });
    }
};
