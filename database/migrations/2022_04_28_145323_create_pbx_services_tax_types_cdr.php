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
        Schema::create('pbx_services_tax_types_cdr', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->unsigned()->nullable();
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedInteger('creator_id')->unsigned()->nullable();
            // $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedInteger('pbx_services_id')->unsigned()->nullable();
            //  $table->foreign('pbx_services_id')->references('id')->on('pbx_services')->onDelete('cascade');

            $table->unsignedInteger('tax_types_id')->unsigned()->nullable();
            // $table->foreign('tax_types_id')->references('id')->on('tax_types')->onDelete('cascade');

            $table->string('name');
            $table->decimal('percent', $precision = 8, $scale = 2);
            $table->tinyInteger('compound_tax');
            $table->enum('status', ['A', 'I']);

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
        Schema::dropIfExists('pbx_services_tax_types_cdr');
    }
};
