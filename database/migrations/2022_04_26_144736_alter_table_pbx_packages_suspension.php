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
        //

        Schema::table('pbx_packages', function (Blueprint $table) {
            $table->dropColumn('suspension_type');
        });

        Schema::table('pbx_packages', function (Blueprint $table) {
            $table->enum('suspension_type', ['T', 'E'])->default('T');
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
