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
        Schema::create('history_call_indi_tax_types', function (Blueprint $table) {
            $table->id();
            $table->decimal('percent', $precision = 8, $scale = 2);
            $table->tinyInteger('compound_tax');
            $table->float('amount', 9, 5)->unsigned()->nullable()->default(0);
            $table->float('tax', 8, 5)->unsigned()->nullable()->default(0);
            $table->morphs('taxable');
            $table->unsignedInteger('pbx_services_id')->nullable();
            $table->foreign('pbx_services_id')->references('id')->on('pbx_services')->onDelete('cascade');
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
        Schema::dropIfExists('history_call_indi_tax_types');
    }
};
