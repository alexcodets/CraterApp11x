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
        Schema::table('history_call_indi', function (Blueprint $table) {
            $table->unsignedInteger('invoice_id')->nullable();

            /* $table->foreign('invoice_id')
                 ->references('id')
                 ->on('invoices')
                 ->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('history_call_indi', function (Blueprint $table) {
            $table->dropForeign('invoice_id');
            $table->dropColumn('invoice_id');
        });
    }
};
