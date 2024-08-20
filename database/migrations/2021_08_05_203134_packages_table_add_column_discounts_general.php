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
            $table->enum('discount_general_type', ['percentage', 'fixed'])->nullable()
            ->after('html');
            $table->decimal('discount_general', 15, 2)->nullable()
            ->after('discount_general_type');
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
            $table->dropColumn(['discount_general_type', 'discount_general']);
        });
    }
};
