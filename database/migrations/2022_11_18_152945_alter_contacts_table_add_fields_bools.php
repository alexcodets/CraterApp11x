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
        if (Schema::hasColumn('contacts', 'email_estimates') == false) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->boolean('email_estimates')->default(0)->nullable();
                $table->boolean('email_invoices')->default(0)->nullable();
                $table->boolean('email_payments')->default(0)->nullable();
                $table->boolean('email_services')->default(0)->nullable();
                $table->boolean('email_pbx_services')->default(0)->nullable();
                $table->boolean('email_tickets')->default(0)->nullable();
            });
        }
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
