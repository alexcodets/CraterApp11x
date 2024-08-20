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
        Schema::table('avalara_configs', function (Blueprint $table) {
            $table->unsignedInteger('item_did_id')->nullable()->after('status');
            $table->unsignedInteger('item_cdr_id')->nullable()->after('item_did_id');
            $table->unsignedInteger('item_extension_id')->nullable()->after('item_cdr_id');
            $table->unsignedInteger('item_international_id')->nullable()->after('item_extension_id');
            /* $table->foreign('item_did_id')->references('id')->on('items');
             $table->foreign('item_cdr_id')->references('id')->on('items');
             $table->foreign('item_extension_id')->references('id')->on('items');
             $table->foreign('item_international_id')->references('id')->on('items');*/

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_configs', function (Blueprint $table) {
            //
        });
    }
};
