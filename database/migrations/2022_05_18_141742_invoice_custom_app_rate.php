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
        //

        Schema::create('invoice_app_rates', function (Blueprint $table) {
            $table->id();
            $table->string('app_name');
            $table->integer('quantity');
            $table->decimal('costo', 10, 2);
            $table->unsignedInteger('pbx_package_id');
            $table->unsignedInteger('pbx_service_id');
            $table->unsignedInteger('invoice_id');
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
        //
        Schema::dropIfExists('invoice_app_rates');
    }
};
