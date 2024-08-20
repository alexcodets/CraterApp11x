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
        Schema::table('profile_did_toll_frees', function (Blueprint $table) {
            $table->integer('toll_free_category_id')->unsigned();
            //$table->foreign('toll_free_category_id','fk_toll_free')->references('id')->on('pbx_categories')->onDelete('cascade');
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
