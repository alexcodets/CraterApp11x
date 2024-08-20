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
        Schema::create('international_rate', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('creator_id')->unsigned()->nullable();
            $table->integer('country_id')->unsigned()->nullable();
            $table->enum('status', ['A', 'I'])->nullable();
            $table->decimal('rate_per_minute', 20, 5)->nullable();
            $table->string('prefix')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('international_rate');
    }
};
