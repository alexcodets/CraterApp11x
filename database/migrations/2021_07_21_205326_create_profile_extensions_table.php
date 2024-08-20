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
        Schema::create('profile_extensions', function (Blueprint $table) {
            $table->increments('id');
            // columns fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            // columns
            $table->string('name', 150);
            $table->string('description', 250)->nullable();
            $table->float('rate', 8, 2)->nullable();
            $table->integer('minutes_cap')->nullable();
            $table->integer('minutes_increments')->nullable();
            $table->decimal('outbound_per_minute_rate', $precision = 8, $scale = 2)->nullable();
            $table->decimal('inbound_per_minute_rate', $precision = 8, $scale = 2)->nullable();
            $table->float('extension_balance', 8, 2)->nullable();
            $table->float('minimum_extension_balance', 8, 2)->nullable();

            $table->enum('status_payment', ['prepaid', 'postpaid']);
            $table->enum('status', ['A', 'I', 'R'])->default('A');
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
        Schema::dropIfExists('profile_extensions');
    }
};
