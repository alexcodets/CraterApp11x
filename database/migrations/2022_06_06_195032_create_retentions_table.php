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
        Schema::create('retentions', function (Blueprint $table) {
            $table->id();
            $table->text('concept');
            $table->bigInteger('minimium_base_per_unit')->unsigned();
            $table->bigInteger('minimium_base_in_currency');
            $table->enum('type_of_minimium_base_in_currency', ['percentage', 'fixed']);
            $table->decimal('percentage', 5, 2);
            $table->boolean('foreing')->default(0);
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries');

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
        Schema::dropIfExists('retentions');
    }
};
