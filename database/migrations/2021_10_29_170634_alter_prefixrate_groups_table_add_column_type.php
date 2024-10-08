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
        Schema::table('prefixrate_groups', function (Blueprint $table) {
            $table->enum('type', ['Inbound', 'Outbound'])->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('prefixrate_groups', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
