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
        Schema::table('pbx_services', function (Blueprint $table) {
            $table->enum('allow_discount_type', ['fixed', 'percentage'])->after('allow_discount_value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_services', function (Blueprint $table) {
            $table->dropColumn('allow_discount_type');
        });
    }
};
