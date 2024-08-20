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

        Schema::table('pbx_packages', function (Blueprint $table) {
            $table->boolean('avalara_options')->default(0)->change();
            $table->boolean('avalara_price')->default(0)->change();
            $table->boolean('avalara_items')->default(0)->change();
            $table->boolean('avalara_extension')->default(0)->change();
            $table->boolean('avalara_did')->default(0)->change();
            $table->boolean('avalara_callrating')->default(0)->change();

        });
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
