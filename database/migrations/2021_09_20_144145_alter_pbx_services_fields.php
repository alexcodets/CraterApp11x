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

        Schema::table('pbx_services', function (Blueprint $table) {
            $table->unsignedBigInteger('pbxpackages_price')->nullable();
            $table->unsignedBigInteger('discount_val')->nullable();
            $table->unsignedBigInteger('sub_total')->nullable();
            $table->unsignedBigInteger('total')->nullable();
            $table->unsignedBigInteger('tax')->nullable();
            $table->unsignedBigInteger('call_rating_preview')->nullable();
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
