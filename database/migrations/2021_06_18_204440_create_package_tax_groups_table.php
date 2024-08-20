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
        Schema::create('package_tax_groups', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('package_id')->unsigned();
            $table->foreign('package_id')->references('id')->on('packages');

            $table->unsignedBigInteger('tax_group_id')->unsigned()->nullable();
            $table->foreign('tax_group_id')->references('id')->on('tax_groups')->onDelete('cascade');

            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');

            $table->enum('status', ['A', 'I'])->nullable();
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
        Schema::dropIfExists('package_tax_groups');
    }
};
