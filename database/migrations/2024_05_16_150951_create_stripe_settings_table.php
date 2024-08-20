<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('stripe_settings', function (Blueprint $table) {
            $table->id();
            $table->string('public_key');
            $table->string('secret_key');
            $table->enum('status', ['A', 'I'])->default('A');
            $table->string('currency', 5)->default('USD');
            $table->string('environment', 20)->default('sandbox');
            $table->unsignedBigInteger('creator_id');
            //$table->foreign('creator_id')->references('id')->on('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }
};
;
