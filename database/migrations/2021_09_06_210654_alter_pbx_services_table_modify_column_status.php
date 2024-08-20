<?php

use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        \DB::statement("ALTER TABLE pbx_services MODIFY COLUMN status ENUM('A', 'P', 'S', 'C')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        \DB::statement("ALTER TABLE pbx_services MODIFY COLUMN status ENUM('A', 'I')");
    }
};
