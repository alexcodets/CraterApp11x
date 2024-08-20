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
        if (Schema::hasColumn('package_items', 'name') == false) {
            Schema::table('package_items', function (Blueprint $table) {
                $table->string('name', 250)->after('description')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('package_items', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
