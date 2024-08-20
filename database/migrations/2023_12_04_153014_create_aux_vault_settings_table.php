<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('aux_vault_settings', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint', 150);
            $table->text('api_key');
            $table->string('merchant_id')->nullable();
            $table->string('currency', 10)->nullable();
            $table->boolean('default')->default(false);
            $table->boolean('production')->default(false);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('company_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aux_vault_settings');
    }
};
