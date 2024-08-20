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
        Schema::create('invoice_customer_packages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('invoice_id')->unsigned()->nullable();
            // $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->integer('package_id')->unsigned()->nullable();
            //$table->foreign('package_id')->references('id')->on('packages')->onDelete('cascade');
            $table->integer('company_id')->unsigned()->nullable();
            $table->integer('customer_id');
            $table->enum('status', ['A', 'D']);
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
        Schema::dropIfExists('invoice_customer_packages');
    }
};
