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
        Schema::create('avalara_configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('conexion');
            $table->string('user_name')->unique()->nullable();
            $table->string('password')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('url')->nullable();
            $table->string('host')->nullable();
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
        Schema::dropIfExists('avalara_configs');
    }
};
