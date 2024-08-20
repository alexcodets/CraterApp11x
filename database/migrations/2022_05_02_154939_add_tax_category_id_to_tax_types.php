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
        Schema::table('tax_types', function (Blueprint $table) {
            //
            $table->foreignId('tax_category_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('tax_types', function (Blueprint $table) {
            $table->dropForeign('tax_category_id');
            $table->dropColumn('tax_category_id');
        });
    }
};
