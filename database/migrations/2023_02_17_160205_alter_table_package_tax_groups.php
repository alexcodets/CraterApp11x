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
        if (Schema::hasTable('package_tax_groups')) {
            if (Schema::hasColumn('package_tax_groups', 'package_id')) {
                Schema::table('package_tax_groups', function (Blueprint $table) {
                    $table->unsignedInteger('package_id')->nullable()->change();
                });
            }
            if (Schema::hasColumn('package_tax_groups', 'pbx_package_id') == false) {
                Schema::table('package_tax_groups', function (Blueprint $table) {
                    $table->unsignedInteger('pbx_package_id')->after('package_id')->unsigned()->nullable();
                    $table->foreign('pbx_package_id')->references('id')->on('pbx_packages')->onDelete('cascade');
                });
            }
            if (Schema::hasColumn('package_tax_groups', 'company_id')) {
                Schema::table('package_tax_groups', function (Blueprint $table) {
                    $table->dropForeign(['company_id']);
                    $table->dropColumn('company_id');
                });
            }
            if (Schema::hasColumn('package_tax_groups', 'status')) {
                Schema::table('package_tax_groups', function (Blueprint $table) {
                    $table->dropColumn('status');
                });
            }
            if (Schema::hasColumn('package_tax_groups', 'deleted_at') == false) {
                Schema::table('package_tax_groups', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
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
