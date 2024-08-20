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
        Schema::create('aditional_charges', function (Blueprint $table) {
            $table->increments('id');
            // columns fk
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('profile_extension_id')->nullable();
            // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            //$table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            ///$table->foreign('profile_extension_id')->references('id')->on('profile_extensions')->onDelete('cascade');
            // columns
            $table->string('description', 250);
            $table->decimal('amount', 8, 2);
            $table->boolean('status');
            // $table->string('currency', 50);

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
        Schema::dropIfExists('aditional_charges');
    }
};
