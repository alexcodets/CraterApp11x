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
        Schema::table('payments_paypals', function (Blueprint $table) {
            // card number alter payment_id
            $table->string('card_number')->after('payment_id')->nullable();
            // card type alter payment_id
            $table->string('card_type')->after('payment_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('payments_paypals', function (Blueprint $table) {
            $table->dropColumn('card_number');
            $table->dropColumn('card_type');
        });
    }
};
