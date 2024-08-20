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
            $table->enum('bscl', ["0", "1"])
                ->after('host')
                ->nullable()->comment('Business Class');

            $table->enum('svcl', ["0", "1"])
                ->after('bscl')
                ->nullable()->comment('Service Class');

            $table->boolean('fclt')
                ->after('svcl')
                ->nullable()->comment('Has Facilities');

            $table->boolean('reg')
                ->after('fclt')
                ->nullable()->comment('Is Regulated');

            $table->boolean('frch')
                ->after('reg')
                ->nullable()->comment('Is Franchise');

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
