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
        Schema::table('customer_package_discounts', function (Blueprint $table) {
            $table->date('start_date')->nullable()->change();
            $table->enum('term_type', ['D', 'U'])->after('discount_val');
            $table->integer('time_unit_number')->after('end_date')->nullable();
            $table->enum('term', ['days', 'weeks', 'months', 'years'])->after('time_unit_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('customer_package_discounts', function (Blueprint $table) {
            $table->dropColumn(['term_type', 'time_unit_number', 'term']);
        });
    }
};
