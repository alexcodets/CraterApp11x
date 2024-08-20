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
        Schema::create('invoice_pbx_did_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id')->unsigned();
            //   $table->foreign('invoice_id')->references('id')->on('invoices');
            $table->string('name', 150);
            $table->unsignedInteger('quantity')->unsigned();
            $table->decimal('price', 10, 2);
            $table->decimal('total', 10, 2);
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
        Schema::dropIfExists('invoice_pbx_did_detail');
    }
};
