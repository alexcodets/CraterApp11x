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
        Schema::create('history_call_indi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('call_detail_register_totals_id');
            $table->float('amout', 9, 5)->unsigned()->nullable()->default(0);

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
        Schema::dropIfExists('history_call_indi');
    }
};
