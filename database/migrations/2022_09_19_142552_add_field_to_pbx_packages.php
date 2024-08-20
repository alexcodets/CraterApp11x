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
            $table->boolean('avalaraBundle')->default(0);
            $table->integer('bundleTransaction')->default(0);
            $table->integer('bundleService')->default(0);
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
            $table->dropColumn('avalaraBundle');
            $table->dropColumn('bundleTransaction');
            $table->dropColumn('bundleService');
        });
    }
};
