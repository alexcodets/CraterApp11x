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
        Schema::table('pbx_services', function (Blueprint $table) {
            $table->unsignedInteger('pbx_tenant_id')->nullable()->change();
            $table->unsignedInteger('customer_id')->nullable();
            $table->date('renewal_date')->nullable();
            // $table->foreign('pbx_tenant_id')->references('id')->on('pbx_tenant')->onDelete('cascade');
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
    }
};
