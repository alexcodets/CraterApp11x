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
        Schema::create('pbx_services_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->integer('count_extension')->unsigned();
            $table->integer('count_did')->unsigned();
            $table->integer('count')->unsigned();
            $table->decimal('price_did', 15, 2);
            $table->string('name');
            $table->softDeletes();
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
        Schema::dropIfExists('pbx_services_detail');
    }
};
