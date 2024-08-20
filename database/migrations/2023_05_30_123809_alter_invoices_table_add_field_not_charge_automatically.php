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
        if (Schema::hasTable('invoices')) {
            if (Schema::hasColumn('invoices', 'not_charge_automatically') == false) {
                Schema::table('invoices', function (Blueprint $table) {
                    $table->boolean('not_charge_automatically')->after('save_as_draft')->default(false);
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
