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
        Schema::create('pbx_packages', function (Blueprint $table) {
            $table->increments('id');
            // fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('taxes_id')->nullable();
            $table->unsignedInteger('package_tax_groups_id')->nullable();
            $table->unsignedInteger('item_group_id')->nullable();

            $table->string('pbx_package_name', 150);
            $table->mediumText('html')->nullable();
            $table->mediumText('text')->nullable();
            // $table->string('description', 250);
            $table->enum('status', ['A', 'I', 'R'])->default('A');
            $table->integer('qty_available')->default(0)->nullable();
            $table->integer('client_limit')->default(0)->nullable();
            $table->boolean('extensions');
            $table->boolean('did');
            $table->boolean('call_ratings');
            $table->boolean('package_discount');
            $table->string('type', 150);
            $table->integer('discount');
            $table->boolean('modify_server');


            $table->timestamps();
            $table->softDeletes();
            /*
                        $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                        $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
                        $table->foreign('taxes_id')->references('id')->on('taxes')->onDelete('cascade');
                        $table->foreign('package_tax_groups_id')->references('id')->on('package_tax_groups')->onDelete('cascade');
                        $table->foreign('item_group_id')->references('id')->on('item_groups')->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pbx_packages');
    }
};
