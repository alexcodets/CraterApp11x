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
        Schema::table('taxes', function (Blueprint $table) {
            $table->unsignedInteger('pbx_package_id')->nullable();
            $table->foreign('pbx_package_id')->references('id')->on('pbx_packages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('taxes', function (Blueprint $table) {
            //
        });
    }
};
