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
        Schema::create('tax_group_taxes', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('tax_group_id');
            $table->foreign('tax_group_id')
                ->references('id')
                ->on('tax_groups')
                ->onDelete('cascade');

            $table->unsignedInteger('tax_types_id');
            $table->foreign('tax_types_id')
                ->references('id')
                ->on('tax_types')
                ->onDelete('cascade');


            $table->integer('company_id')->nullable();
            /* $table->foreign('company_id')
                 ->references('id')
                 ->on('companies')
                 ->onDelete('cascade');*/

            $table->enum('status', ['A', 'I'])->default('A');
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
        Schema::dropIfExists('tax_group_taxes');
    }
};
