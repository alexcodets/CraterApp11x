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
        if (Schema::hasTable('profile_did_toll_frees')) {
            if (Schema::hasColumn('profile_did_toll_frees', 'rate_per_minute')) {
                Schema::table('profile_did_toll_frees', function (Blueprint $table) {
                    $table->decimal('rate_per_minute', 20, 2)->nullable()->change();
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
