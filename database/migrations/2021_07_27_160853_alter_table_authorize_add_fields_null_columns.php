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
        Schema::table('authorize', function (Blueprint $table) {
            $table->integer('cc_number')->nullable()->change();
            $table->integer('expiry_month')->nullable()->change();
            $table->integer('expiry_year')->nullable()->change();
            $table->integer('cvv')->nullable()->change();
            $table->integer('payment_id')->nullable()->change();
            $table->string('card_number')->after('payment_status');
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
