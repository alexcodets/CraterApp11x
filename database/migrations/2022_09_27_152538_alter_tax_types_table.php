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
        if (Schema::hasColumn('tax_types', 'status') == false) {
            Schema::table('tax_types', function (Blueprint $table) {
                $table->enum('status', ['A', 'I'])->default('A'); // New field
                $table->softDeletes(); // New field
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
        Schema::dropIfExists('tax_types');
    }
};
