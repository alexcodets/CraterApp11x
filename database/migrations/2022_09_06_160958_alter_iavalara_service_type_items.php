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
        //


        DB::statement("ALTER TABLE items MODIFY COLUMN avalara_type ENUM('0','1','2','3','4','5','6','7','8','9','10','11','13','16','18','19', '20','21', '59', '65')  NULL ");
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
