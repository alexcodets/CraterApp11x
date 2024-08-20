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
        Schema::create('profile_did', function (Blueprint $table) {
            $table->increments('id');
            // columns fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            // columns
            $table->string('name', 150);
            $table->string('description', 250)->nullable();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('did_rate')->nullable();
            $table->integer('toll_free_did_rate')->nullable();
            $table->integer('international_did_rate')->nullable();
            $table->integer('international_inbound_per_minute_rate')->nullable();
            $table->integer('inbound_per_minute_rate')->nullable();
            $table->integer('emergency_services_rate')->nullable();
            $table->string('emergency_services_address', 250)->nullable();
            $table->integer('cnam_rate')->nullable();
            $table->string('cnam_name', 150)->nullable();
            $table->decimal('cnam_price', 8, 2)->nullable();

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
        Schema::dropIfExists('profile_did');
    }
};
