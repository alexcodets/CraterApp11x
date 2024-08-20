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

        DB::statement("ALTER TABLE items MODIFY COLUMN avalara_type ENUM('0','1','2','3','4','5','6','7','8','9','10','11','13','14','15','16','18','19', '20','21','24','25','32','34','36','42','44','47','48', '50','57','58','59','60','61','64', '65','66')  NULL ");
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
