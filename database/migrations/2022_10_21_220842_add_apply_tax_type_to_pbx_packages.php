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
        Schema::table('pbx_packages', function (Blueprint $table) {
            // apply_tax_type  type enum enum( 'item', 'general')
            $table->enum('apply_tax_type', ['item', 'general'])->default('general')->after('item_group_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_packages', function (Blueprint $table) {
            //
        });
    }
};
