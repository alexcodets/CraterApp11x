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
        Schema::create('modules', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('name', ['PBXware', 'Avalara']);
            $table->string('description', 250);
            $table->string('version', 50);
            $table->string('image', 250);
            $table->enum('status', ['A', 'I'])->default('A');
            $table->string('slug', 250);
            // $table->dateTime('delete_at');
            // $table->dateTime('created_at');
            // $table->dateTime('updated_at');

            $table->integer('company_id')->unsigned()->nullable();
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->integer('creator_id')->unsigned()->nullable();
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('modules');
    }
};
