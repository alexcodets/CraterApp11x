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
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->renameColumn('date_begin', 'activation_date');
            $table->enum('term', [
                'daily',
                'weekly',
                'monthly',
                'bimonthly',
                'quarterly',
                'biannual',
                'yearly',
                'one time'
            ])
            ->after('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->dropColumn('term');
            $table->renameColumn('activation_date', 'date_begin');
        });
    }
};
