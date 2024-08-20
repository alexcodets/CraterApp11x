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
        //

        if (Schema::hasColumn('pbx_services_extensions', 'invoiced_prorate') == false) {

            Schema::table('pbx_services_extensions', function (Blueprint $table) {
                //
                $table->boolean('invoiced_prorate')->default(0);
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
        //
    }
};
