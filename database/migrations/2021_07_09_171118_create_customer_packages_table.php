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
        Schema::create('customer_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('package_id');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('company_id');
            $table->enum('discount_type', ['N', 'G', 'I']);
            $table->enum('status', ['A', 'P', 'S', 'C']);
            $table->date('date_begin')->nullable();
            $table->foreign('customer_id')->references('id')->on('users')->onDelete('CASCADE');
            $table->foreign('package_id')->references('id')->on('packages')->onDelete('CASCADE');
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
        Schema::dropIfExists('customer_packages');
    }
};
