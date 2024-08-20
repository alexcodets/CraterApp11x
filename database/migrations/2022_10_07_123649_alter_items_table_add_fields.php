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
        if (Schema::hasColumn('items', 'deleted_at') == false) {
            Schema::table('items', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (Schema::hasColumn('items', 'status') == false) {
            Schema::table('items', function (Blueprint $table) {
                $table->enum('status', ['A', 'I'])->default('A');
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
        Schema::dropIfExists('items');
    }
};
