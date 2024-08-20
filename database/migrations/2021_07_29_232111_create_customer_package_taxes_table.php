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
        Schema::create('customer_package_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('tax_type_id');
            $table->unsignedInteger('customer_package_id')->nullable();
            $table->unsignedBigInteger('customer_package_item_id')->nullable();
            $table->unsignedInteger('creator_id');
            $table->unsignedInteger('company_id');
            $table->string('name');
            $table->decimal('percent', 5, 2);
            $table->unsignedBigInteger('amount');
            $table->boolean('compound_tax');
            $table->foreign('customer_package_id')->references('id')->on('customer_packages')->onDelete('CASCADE');
            $table->foreign('customer_package_item_id')->references('id')->on('customer_package_items')->onDelete('CASCADE');
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
        Schema::dropIfExists('customer_package_taxes');
    }
};
