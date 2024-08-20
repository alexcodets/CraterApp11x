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
        Schema::create('push_notifications_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->timestamp('date')->nullable();
            $table->string('message', 255)->nullable();
            $table->boolean('notification_sent')->nullable();
            $table->string('log_message', 255)->nullable();
            $table->json('notification_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            //  $table->foreign('customer_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('push_notifications_logs');
    }
};
