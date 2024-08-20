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
        Schema::table('contacts', function (Blueprint $table) {

            $table->enum('type', ['B', 'S', 'R'])->default('B')->after('position');
            $table->boolean('log_in_credentials')->nullable()->after('position');
            $table->string('password')->after('position');
            $table->string('repeat_password')->after('position');
            $table->boolean('invoices')->nullable()->after('position');
            $table->boolean('estimates')->nullable()->after('position');
            $table->boolean('payments')->nullable()->after('position');
            $table->boolean('tickets')->nullable()->after('position');
            $table->boolean('payments_accounts')->nullable()->after('position');
            $table->boolean('reports')->nullable()->after('position');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('log_in_credentials');
            $table->dropColumn('password');
            $table->dropColumn('repeat_password');
            $table->dropColumn('invoices');
            $table->dropColumn('estimates');
            $table->dropColumn('payments');
            $table->dropColumn('tickets');
            $table->dropColumn('payments_accounts');
            $table->dropColumn('reports');
        });
    }
};
