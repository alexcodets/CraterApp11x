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
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->renameColumn('discount_type', 'discount_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->renameColumn('discount_by', 'discount_type');
        });
    }
};
