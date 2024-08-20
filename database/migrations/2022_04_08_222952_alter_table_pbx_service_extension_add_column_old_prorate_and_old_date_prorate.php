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
        Schema::table('pbx_services_extensions', function (Blueprint $table) {
            $table->bigInteger('old_prorate')->nullable();
            $table->date('old_date_prorate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_services_extensions', function (Blueprint $table) {
            $table->dropColumn(['old_prorate', 'old_date_prorate']);
        });
    }
};
