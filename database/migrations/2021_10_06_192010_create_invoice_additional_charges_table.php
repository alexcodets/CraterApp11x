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
        Schema::create('invoice_additional_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('invoice_id');
            $table->unsignedInteger('additional_charge_id');
            $table->string('additional_charge_name');
            $table->decimal('additional_charge_amount');
            $table->string('template_name');
            $table->string('additional_charge_type', 100);
            $table->integer('qty');
            $table->decimal('total')->default(0);

            /*$table->foreign('invoice_id')
                ->references('id')
                ->on('invoices')
                ->onDelete('cascade');

            $table->foreign('additional_charge_id')
                ->references('id')
                ->on('aditional_charges')
                ->onDelete('cascade');*/

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
        Schema::dropIfExists('invoice_additional_charges');
    }
};
