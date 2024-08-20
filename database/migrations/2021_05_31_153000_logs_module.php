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
        //

        Schema::create('logsmodule', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('module', 255)->nullable();
            $table->string('task', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->integer('task_id')->unsigned()->nullable();
            $table->string('username', 255)->nullable();
            $table->string('useremail', 255)->nullable();
            $table->string('role', 255)->nullable();
            $table->integer('company_id')->unsigned()->nullable();
            $table->string('deletemessage', 255)->nullable();
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
        //
        Schema::dropIfExists('logsmodule');
    }
};
