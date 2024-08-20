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
        Schema::table('service_tickets', function (Blueprint $table) {
            $table->dropColumn('service_type');
        });

        Schema::table('service_tickets', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_ticket_id')->after('service_id');
            $table->string('service_type')->after('customer_ticket_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('service_tickets', function (Blueprint $table) {
            $table->dropColumn('customer_ticket_id');
        });
    }
};
