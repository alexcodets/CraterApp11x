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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('prepaid_option', ["0", "1"])->default("0")
                ->after('avalara_type')
                ->nullable();
            $table->enum('auto_debit', ["0", "1"])
                ->after('prepaid_option')
                ->nullable();
            $table->integer('email_low_balance_notification')->default(0)
                ->after('auto_debit')
                ->nullable();
            $table->integer('auto_replenish_amount')->default(0)
                ->after('email_low_balance_notification')
                ->nullable();
            $table->integer('negative_balance')->default(0)
                ->after('auto_replenish_amount')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['prepaid_option', 'auto_debit', 'email_low_balance_notification', 'auto_replenish_amount', 'negative_balance']);
        });
    }
};
