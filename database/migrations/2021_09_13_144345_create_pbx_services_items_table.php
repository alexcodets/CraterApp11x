<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void­
     */
    public function up(): void­
    {
        Schema::create('pbx_services_items', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('pbx_services_id')->unsigned();
            //   $table->foreign('pbx_services_id')->references('id')->on('pbx_services');

            $table->integer('item_group_id')->unsigned();
            //   $table->foreign('item_group_id')->references('id')->on('item_groups');

            $table->integer('items_id')->unsigned();
            //  $table->foreign('items_id')->references('id')->on('items');

            $table->integer('company_id')->unsigned()->nullable();
            //   $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->unsignedInteger('creator_id')->unsigned()->nullable();
            //$table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('discount_type');
            $table->decimal('quantity', 15, 2);
            $table->decimal('discount', 15, 2)->nullable();
            $table->unsignedBigInteger('discount_val')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('tax');
            $table->unsignedBigInteger('total');
            $table->enum('status', ['A', 'I'])->nullable();
            $table->string('description')->nullable();


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
        Schema::dropIfExists('pbx_services_items');
    }
};
