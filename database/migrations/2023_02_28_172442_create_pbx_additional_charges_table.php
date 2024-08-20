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
        Schema::create('pbx_additional_charges', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_id')->nullable();
            $table->unsignedInteger('creator_id')->nullable();
            $table->unsignedInteger('profile_extension_id')->nullable();
            $table->unsignedInteger('profile_did_id')->nullable();
            $table->string('description', 250);
            $table->decimal('amount', 8, 2);
            $table->decimal('total', 8, 2);
            $table->integer('quantity');
            $table->boolean('status');
            $table->unsignedInteger('pbx_service_id')->nullable();
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
        Schema::dropIfExists('pbx_additional_charges');
    }
};
