<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('package_groups', function (Blueprint $table) {
            DB::statement("ALTER TABLE package_groups MODIFY COLUMN status ENUM('A', 'I')");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('package_groups', function (Blueprint $table) {
            //
        });
    }
};
