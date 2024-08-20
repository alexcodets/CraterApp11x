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
        Schema::create('pbx_services_prefixrate_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pbx_service_id')->unsigned();
            //$table->foreign('pbx_service_id')->references('id')->on('pbx_services');
            $table->unsignedInteger('prefixrate_group_id')->unsigned();
            //$table->foreign('prefixrate_group_id')->references('id')->on('prefixrate_groups');
            $table->enum('type', ['Inbound', 'Outbound']);
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
        Schema::dropIfExists('pbx_services_prefixrate_groups');
    }
};
