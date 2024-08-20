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
        Schema::create('package_group', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_groups_id')->unsigned();
            $table->foreign('package_groups_id')->references('id')->on('package_groups')->onDelete('cascade');
            $table->integer('packages_id')->unsigned();
            $table->foreign('packages_id')->references('id')->on('packages')->onDelete('cascade');
            $table->enum('status', ['A', 'T']);
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
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
        Schema::dropIfExists('package_group');
    }
};
