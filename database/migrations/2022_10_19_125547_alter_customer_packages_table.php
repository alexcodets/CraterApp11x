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
        if (Schema::hasColumn('customer_packages', 'addresses_id') == false) {
            Schema::table('customer_packages', function (Blueprint $table) {
                $table->integer('addresses_id')->after('service_auto_suspension')->unsigned()->nullable();
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
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->dropColumn('addresses_id');
        });
    }
};
