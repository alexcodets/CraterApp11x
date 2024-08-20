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
        Schema::table('avalara_configs', function (Blueprint $table) {
            $table->boolean('lfln')->after('frch')->nullable()->comment('The customer is a Lifeline participant');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_configs', function (Blueprint $table) {
            //
        });
    }
};
