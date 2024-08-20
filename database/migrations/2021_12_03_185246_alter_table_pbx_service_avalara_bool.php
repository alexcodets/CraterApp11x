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
            $table->boolean('avalara_options')->default(0);

            $table->boolean('avalara_price')->default(1);
            $table->boolean('avalara_items')->default(1);
            $table->boolean('avalara_extension')->default(1);
            $table->boolean('avalara_did')->default(1);
            ;
            $table->boolean('avalara_callrating')->default(1);

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
