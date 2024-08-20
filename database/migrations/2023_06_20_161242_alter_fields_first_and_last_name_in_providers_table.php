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
        if (Schema::hasTable('providers')) {
            if (Schema::hasColumn('providers', 'first_name')) {
                Schema::table('providers', function (Blueprint $table) {
                    $table->string('first_name')->nullable()->change();
                });
            }
            if (Schema::hasColumn('providers', 'last_name')) {
                Schema::table('providers', function (Blueprint $table) {
                    $table->string('last_name')->nullable()->change();
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
