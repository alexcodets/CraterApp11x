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

        Schema::table('history_call_indi', function (Blueprint $table) {
            $table->unsignedBigInteger('tax_type_id')->nullable();
            $table->float('taxamount', 9, 5)->unsigned()->nullable()->default(0);
            $table->unsignedTinyInteger('type')->default(0)->comment('0 => no impuesto, 1 => Tax cdr, 2 => all taxes, 3 => error');

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
