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
        Schema::create('item_group_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('item_group_id');
            $table->unsignedInteger('item_id');
            $table->unsignedInteger('company_id')->nullable();
            $table->enum('status', ['A', 'I'])->default('A');

            $table->foreign('item_group_id')
                ->references('id')
                ->on('item_groups')
                ->onDelete('cascade');

            $table->foreign('item_id')
                ->references('id')
                ->on('items')
                ->onDelete('cascade');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onDelete('cascade');

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
        Schema::dropIfExists('item_group_items');
    }
};
