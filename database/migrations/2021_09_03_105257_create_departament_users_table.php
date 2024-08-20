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
        Schema::create('departament_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('dep_group_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('company_id')->nullable();

            /*  $table->foreign('dep_group_id')
                  ->references('id')
                  ->on('ticket_departaments')
                  ->onDelete('cascade');

              $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

              $table->foreign('company_id')
                  ->references('id')
                  ->on('companies')
                  ->onDelete('cascade');*/

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
        Schema::dropIfExists('departament_users');
    }
};
