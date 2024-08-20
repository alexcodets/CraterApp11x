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

            $table->enum('bscl', ["0", "1"])
                ->after('avalara_type')
                ->nullable();

            $table->enum('svcl', ["0", "1"])
                ->after('bscl')
                ->nullable();

            $table->boolean('fclt')
                ->after('svcl')
                ->nullable();

            $table->boolean('reg')
                ->after('fclt')
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
            $table->dropColumn(['bscl', 'svcl', 'fclt', 'reg']);
        });
    }
};
