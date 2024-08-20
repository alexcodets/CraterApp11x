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
        Schema::create('profile_did_toll_frees', function (Blueprint $table) {
            $table->id();
            // columns fk
            $table->integer('prefijo')->unsigned()->nullable();
            $table->enum('status', ['A', 'I'])->default('A');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

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
        Schema::dropIfExists('profile_did_toll_frees');
    }
};
