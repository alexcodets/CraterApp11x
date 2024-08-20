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
        Schema::create('pbx_servers', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('country_id')->nullable();

            $table->string('server_label', 150);
            $table->string('hostname', 150);
            $table->string('ssl_port', 50);
            $table->string('api_key', 250);
            $table->string('national_dialing_code', 50);
            $table->string('international_dialing_code', 50);
            $table->enum('status', ['A', 'I'])->default('A');

            $table->timestamps();
            $table->softDeletes();

            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            //s$table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pbx_servers');
    }
};
