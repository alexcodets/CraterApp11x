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
        Schema::create('avalara_bundles', function (Blueprint $table) {
            $table->id();
            $table->string('transaction')->nullable();
            $table->string('service')->nullable();
            $table->string('description')->nullable();
            $table->string('charge');
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
        Schema::dropIfExists('avalara_bundles');
    }
};
