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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('customer_type', ['N', 'B', 'R'])
                ->after('company_id')
                ->nullable();
            $table->boolean('avalara_bool')
                ->after('customer_type')
                ->nullable();
            $table->enum('avalara_type', ["0", "1", "2", "3"])
                ->after('avalara_bool')
                ->comment('Avalara Customer Type.')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['customer_type', 'avalara_bool', 'avalara_type']);
        });
    }
};
