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
        Schema::table('hold_invoices', function (Blueprint $table) {
            $table->string('tip_type')->nullable();
            $table->decimal('tip')->nullable();
            $table->bigInteger('tip_val')->nullable();
        });
        Schema::table('core_pos_histories', function (Blueprint $table) {
            $table->string('tip_type')->nullable();
            $table->bigInteger('tip_amount')->nullable();
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
