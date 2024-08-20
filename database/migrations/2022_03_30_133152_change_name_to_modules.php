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
        Schema::table('modules', function (Blueprint $table) {
            // change type of name from enum to string
            $table->string('name', 250)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            // change type of name from string to enum
            $table->enum('name', ['PBXware', 'Avalara'])->change();
        });
    }
};
