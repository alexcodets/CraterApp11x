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
        Schema::table('pbx_services_did', function (Blueprint $table) {
            //
            $table->integer('custom_did_id')->unsigned()->nullable();
            // $table->foreign('custom_did_id')->references('id')->on('profile_did_toll_frees')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pbx_services_did', function (Blueprint $table) {
            $table->dropColumn('custom_did_id');
            /* $table->integer('custom_did_id')->unsigned()->nullable();
            $table->foreign('custom_did_id')->references('id')->on('profile_did_toll_frees')->onDelete('cascade'); */
        });
    }
};
