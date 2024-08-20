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
        Schema::table('avalara_bundles', function (Blueprint $table) {
            //
            $table->dropColumn('charge');
            $table->text('description')->nullable()->change();

            $table->after('description', function ($table) {
                $table->string('name');
                $table->unsignedBigInteger('price');
                $table->enum('status', ['A', 'I'])->default('A');
                $table->boolean('no_taxable')->nullable();
                $table->boolean('allow_taxes')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->integer('company_id')->unsigned()->nullable();
            });

            $table->softDeletes();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //$table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_bundles', function (Blueprint $table) {
            //
        });
    }
};
