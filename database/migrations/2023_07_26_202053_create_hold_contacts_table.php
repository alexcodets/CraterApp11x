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
        if (Schema::hasTable('hold_contacts')) {
            Schema::dropIfExists('hold_contacts');
        }

        Schema::create('hold_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('identification')->nullable();
            $table->string('phone')->nullable();
            $table->string('second_phone')->nullable();
            $table->string('email')->nullable();
            $table->unsignedBigInteger('hold_invoice_id');
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
        Schema::dropIfExists('hold_contacts');
    }
};
