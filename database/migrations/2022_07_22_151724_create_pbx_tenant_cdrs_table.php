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
        Schema::create('pbx_tenant_cdrs', function (Blueprint $table) {
            $table->id();
            //$table->timestamps();
            $table->string('from', 60);
            $table->string('to', 60);
            $table->unsignedInteger('start_date');
            $table->unsignedInteger('duration');
            $table->unsignedInteger('billing_duration')->nullable();
            //$table->unsignedFloat('cost', 9, 5)->nullable();
            $table->string('status', 30); //(8 ⇒ "Answered", 4 ⇒ "Not Answered", 2 ⇒ "Busy", 1 ⇒ "Failed"
            $table->string('unique_id', 25);
            $table->tinyInteger('type')->comment('inbound(0) / outbound(1) / internatl(3)')->nullable();
            $table->integer('trunk_id')->nullable();
            $table->unsignedBigInteger('pbx_cdr_tenant_id')->nullable();
            $table->foreign('pbx_cdr_tenant_id')->references('id')->on('pbx_cdr_tenants')->onDelete('cascade');

            $table->index(['unique_id', 'start_date']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('pbx_tenant_cdrs');
    }
};
