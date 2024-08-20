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
        Schema::table('pbx_additional_charges', function (Blueprint $table) {
            $table->integer('additional_charge_id')->unsigned()->nullable();
            // $table->foreign('additional_charge_id')->references('id')->on('aditional_charges');
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
