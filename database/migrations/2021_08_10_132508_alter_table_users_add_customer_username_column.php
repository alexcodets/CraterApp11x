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
            $table->string('customer_username')->nullable()->after('phone');
            $table->string('password_encrypted')->nullable()->after('password');
            $table->tinyInteger('authentication')->nullable()->after("role");
            $table->tinyInteger('username_status')->nullable()->after('authentication');
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
