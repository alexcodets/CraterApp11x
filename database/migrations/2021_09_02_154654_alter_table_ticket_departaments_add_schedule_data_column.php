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
        Schema::table('ticket_departaments', function (Blueprint $table) {
            $table->longText('schedule_data')->nullable();
            $table->Text('receive_tickets_emails')->nullable();
            $table->Text('receive_mobile_tickets_emails')->nullable();
            $table->Text('receive_tickets_messenger_notifications')->nullable();
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
