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
        if (Schema::hasTable('invoice_additional_charges')) {
            if (Schema::hasColumn('invoice_additional_charges', 'additional_charge_id')) {
                Schema::table('invoice_additional_charges', function (Blueprint $table) {
                    $table->unsignedInteger('additional_charge_id')->nullable()->change();
                });
            }
        }
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
