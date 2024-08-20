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
        if (Schema::hasColumn('taxes', 'package_id') == false) {
            Schema::table('taxes', function (Blueprint $table) {
                $table->integer('package_id')->after('pbx_service_item_id')->unsigned()->nullable();
            });
        }
        if (Schema::hasColumn('taxes', 'package_item_id') == false) {
            Schema::table('taxes', function (Blueprint $table) {
                $table->integer('package_item_id')->after('package_id')->unsigned()->nullable();
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
        Schema::dropIfExists('taxes');
    }
};
