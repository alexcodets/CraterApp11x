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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('type_vat_regime')->nullable();
            $table->boolean('great_contributor')->default(false);
            $table->boolean('self_retaining')->default(false);
            $table->boolean('vat_withholding_agent')->default(false);
            $table->boolean('simple_tax_regime')->default(false);
            $table->boolean('not_applicable_others')->default(false);
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
            $table->dropColumn('not_applicable_others');
        });
    }
};
