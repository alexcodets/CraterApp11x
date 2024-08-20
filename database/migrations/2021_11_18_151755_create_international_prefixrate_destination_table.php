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
        Schema::create('international_prefixrate_destination', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('int_rate_id');
            $table->unsignedBigInteger('prefixrate_id');
            $table->unsignedInteger('company_id');

            /* $table->foreign('int_rate_id')
                 ->references('id')
                 ->on('international_rate')
                 ->onDelete('cascade');

             $table->foreign('prefixrate_id')
                 ->references('id')
                 ->on('prefixrate_groups')
                 ->onDelete('cascade');*/

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('international_prefixrate_destination');
    }
};
