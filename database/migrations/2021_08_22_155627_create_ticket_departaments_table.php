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
        Schema::create('ticket_departaments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 128);
            $table->text('description')->nullable();
            $table->boolean('client_permission')->nullable();
            $table->string('email');
            $table->boolean('sender_override')->nullable();
            $table->boolean('send_emails')->nullable();
            $table->boolean('automatically_transition_admin')->nullable();
            $table->enum('default_priority', ['E', 'C','H','M','L'])->default('E');
            $table->enum('email_handling', ['N', 'P','O','I'])->default('N');
            $table->integer('automatically_close')->unsigned();
            $table->integer('automatically_delete')->unsigned();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('company_id')->unsigned();
            $table->integer('creator_id')->unsigned();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_departaments');
    }
};
