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
        Schema::create('customer_package_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_package_id');
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('company_id');
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->decimal('discount', 15, 2);
            $table->unsignedBigInteger('discount_val');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->foreign('customer_package_id')->references('id')->on('customer_packages')->onDelete('CASCADE');
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
        Schema::dropIfExists('customer_package_discounts');
    }
};
