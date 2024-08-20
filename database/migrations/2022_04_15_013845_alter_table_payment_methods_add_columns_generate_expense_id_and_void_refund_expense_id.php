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
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->boolean('void_refund')->nullable();
            $table->unsignedInteger('generate_expense_id')->nullable();
            $table->unsignedInteger('void_refund_expense_id')->nullable();

            $table->foreign('generate_expense_id')
                ->references('id')
                ->on('expense_categories')
                ->onDelete('cascade');

            $table->foreign('void_refund_expense_id')
                ->references('id')
                ->on('expense_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('payment_methods', function (Blueprint $table) {
            $table->dropForeign(['generate_expense_id']);
            $table->dropForeign(['void_refund_expense_id']);
            $table->dropColumn(['void_refund', 'generate_expense_id', 'void_refund_expense_id']);
        });
    }
};
