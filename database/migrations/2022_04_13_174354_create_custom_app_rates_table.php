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
        Schema::create('custom_app_rates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 8, 2);
            $table->boolean('office')->default(true);
            $table->boolean('bussiness')->default(true);
            $table->boolean('web')->default(true);
            $table->boolean('agent')->default(true);
            $table->boolean('supervisor')->default(true);
            $table->boolean('mobile')->default(true);
            $table->boolean('crm')->default(true);
            $table->boolean('call_pop_up')->default(true);
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
        Schema::dropIfExists('custom_app_rates');
    }
};
