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
        Schema::create('tax_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_groups_id')->unsigned();
            $table->foreign('tax_groups_id')->references('id')->on('tax_groups')->onDelete('cascade');
            $table->unsignedInteger('taxes_id')->unsigned();
            $table->foreign('taxes_id')->references('id')->on('taxes')->onDelete('cascade');
            $table->enum('status', ['A', 'I']);
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
        Schema::dropIfExists('tax_group');
    }
};
