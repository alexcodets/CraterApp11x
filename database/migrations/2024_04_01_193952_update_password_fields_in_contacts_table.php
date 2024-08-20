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
            // Verificar si el campo 'password' existe y modificarlo
            if (Schema::hasColumn('contacts', 'password')) {
                $table->string('password', 255)->change();
            }

            // Verificar si el campo 'repeat_password' existe y modificarlo
            if (Schema::hasColumn('contacts', 'repeat_password')) {
                $table->string('repeat_password', 255)->change();
            }
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
            //
        });
    }
};
