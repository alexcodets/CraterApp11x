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
        Schema::create('pbx_cdr_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->unsignedInteger('pbx_server_id');
            $table->unsignedSmallInteger('tenantid');
            $table->unsignedTinyInteger('status')->default(1);
            $table->foreign('pbx_server_id')->references('id')->on('pbx_servers');
            $table->date('date_begin')->nullable();

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
        Schema::dropIfExists('pbx_cdr_tenants');
    }
};
