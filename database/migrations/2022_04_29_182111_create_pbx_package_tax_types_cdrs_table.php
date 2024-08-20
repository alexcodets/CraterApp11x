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
        Schema::create('pbx_package_tax_types_cdrs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('pbx_package_id')->nullable();
            $table->unsignedInteger('tax_types_id')->nullable();

            // columns
            $table->string('name', 255);
            $table->decimal('percent', 8, 2);
            $table->integer('compound_tax');
            $table->enum('status', ['A', 'I'])->default('A');
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
        Schema::dropIfExists('pbx_package_tax_types_cdrs');
    }
};
