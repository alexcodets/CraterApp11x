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
            $table->enum('discount_term_type', ['D', 'U'])->default('D');
            $table->date('discount_start_date')->nullable();
            $table->date('discount_end_date')->nullable();
            $table->integer('discount_time_units')->nullable();
            $table->enum('discount_term', ['days', 'weeks', 'months', 'years'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
