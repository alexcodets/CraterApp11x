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
            $table->boolean('service_auto_suspension')->after('renewal_date');
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
            $table->dropColumn('service_auto_suspension');
        });
    }
};
