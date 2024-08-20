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
        Schema::create('corebill_logs_dev', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ip', 45)->nullable();
            $table->string('auth', 255)->nullable();
            $table->string('controller', 255)->nullable();
            $table->string('method', 255)->nullable();
            $table->text('request')->nullable();
            $table->text('headers')->nullable();
            $table->text('data_in')->nullable();
            $table->text('data_out')->nullable();
            $table->text('message')->nullable();
            $table->double('time')->nullable();
            $table->enum('type', ['D', 'E'])->default('D');
            $table->dateTime('date_reg');
            $table->integer('company')->unsigned();
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
        Schema::dropIfExists('corebill_logs_dev');
    }
};
