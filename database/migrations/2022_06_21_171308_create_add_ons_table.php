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
        Schema::create('add_ons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 150);
            $table->string('description', 250);
            $table->string('version', 50);
            $table->string('image', 250);
            $table->enum('status', ['A', 'I'])->default('A');
            $table->string('slug', 250);
            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->integer('creator_id')->unsigned()->nullable();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('module_id')->unsigned()->nullable();
            $table->foreign('module_id')->references('id')->on('modules')->onDelete('cascade');
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
        Schema::dropIfExists('add_ons');
    }
};
