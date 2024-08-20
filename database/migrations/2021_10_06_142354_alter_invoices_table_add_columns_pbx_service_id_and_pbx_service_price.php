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
        Schema::table('invoices', function (Blueprint $table) {
            $table->unsignedInteger('pbx_service_id')
                ->after('customer_packages_id')
                ->nullable();

            $table->decimal('pbx_service_price')
                ->after('total')
                ->default(0);

            /*  $table->foreign('pbx_service_id')
                  ->references('id')
                  ->on('pbx_services')
                  ->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['pbx_service_id']);
            $table->dropColumn(['pbx_service_id', 'pbx_service_price']);
        });
    }
};
