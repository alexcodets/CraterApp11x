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
        Schema::create('avalara_logs', function (Blueprint $table) {
            $table->id();
            //[0 => Invoice, 1 => Service Invoice]
            $table->unsignedTinyInteger('type')->nullable();
            $table->boolean('commit')->default(false);
            $table->boolean('status')->default(true);
            $table->json('response')->nullable();
            $table->unsignedInteger('invoice_id')->unsigned();
            $table->unsignedInteger('pbx_service_id')->nullable();
            $table->unsignedInteger('user_id')->unsigned()->nullable();
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
        Schema::dropIfExists('avalara_logs');
    }
};
