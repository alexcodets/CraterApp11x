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
        Schema::create('avalara_invoices', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedInteger('pbx_service_id')->nullable();
            $table->unsignedInteger('invoice_id')->nullable();
            //  $table->foreign('pbx_service_id')->references('id')->on('pbx_services')->onDelete('cascade');
            // $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('avalara_invoices');
    }
};
