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
        Schema::table('profile_did_toll_frees', function (Blueprint $table) {
            DB::statement("ALTER TABLE profile_did_toll_frees MODIFY prefijo BIGINT(20)");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE profile_did_toll_frees MODIFY rate INT");
    }
};
