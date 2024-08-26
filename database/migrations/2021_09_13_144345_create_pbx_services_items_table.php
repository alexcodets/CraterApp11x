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
        Schema::create('pbx_services_items', function (Blueprint $table) {
            $table->increments('id');

            // Cambiar a unsignedBigInteger si las IDs son de tipo bigint
            $table->unsignedBigInteger('pbx_services_id');
            // Descomentar si deseas agregar la clave foránea
            // $table->foreign('pbx_services_id')->references('id')->on('pbx_services');

            $table->unsignedBigInteger('item_group_id');
            // Descomentar si deseas agregar la clave foránea
            // $table->foreign('item_group_id')->references('id')->on('item_groups');

            $table->unsignedBigInteger('items_id');
            // Descomentar si deseas agregar la clave foránea
            // $table->foreign('items_id')->references('id')->on('items');

            $table->unsignedBigInteger('company_id')->nullable();
            // Descomentar si deseas agregar la clave foránea
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            $table->unsignedBigInteger('creator_id')->nullable();
            // Descomentar si deseas agregar la clave foránea
            // $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

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
