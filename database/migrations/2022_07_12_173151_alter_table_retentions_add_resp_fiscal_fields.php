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
        Schema::table('retentions', function (Blueprint $table) {
            $table->integer('type_vat_regime')->nullable()->after('country_id');
            $table->tinyInteger('great_contributor')->default(0)->after('country_id');
            $table->tinyInteger('self_retaining')->default(0)->after('country_id');
            $table->tinyInteger('vat_withholding_agent')->default(0)->after('country_id');
            $table->tinyInteger('simple_tax_regime')->default(0)->after('country_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type_vat_regime');
            $table->dropColumn('great_contributor');
            $table->dropColumn('self_retaining');
            $table->dropColumn('vat_withholding_agent');
            $table->dropColumn('simple_tax_regime');

        });
    }
};
