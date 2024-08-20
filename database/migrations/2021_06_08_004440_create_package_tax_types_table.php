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
        Schema::create('package_tax_types', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->foreign('package_id')
            ->references('id')->on('packages');
            $table->integer('tax_types_id')->unsigned();
            $table->foreign('tax_types_id')
            ->references('id')->on('tax_types');
            $table->string('name');
            $table->decimal('percent', 5, 2);
            $table->tinyInteger('compound_tax')->default(0);
            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('package_tax_types');
    }
};
