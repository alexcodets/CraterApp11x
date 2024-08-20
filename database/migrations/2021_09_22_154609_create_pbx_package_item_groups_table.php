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
        Schema::create('pbx_package_item_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('pbx_package_id')->unsigned();
            // $table->foreign('pbx_package_id')->references('id')->on('pbx_packages')->onDelete('cascade');
            $table->unsignedInteger('item_group_id')->unsigned();
            // $table->foreign('item_group_id')->references('id')->on('item_groups')->onDelete('cascade');
            $table->enum('status', ['A', 'I'])->default('A');
            $table->integer('company_id')->unsigned()->nullable();
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('pbx_package_item_groups');
    }
};
