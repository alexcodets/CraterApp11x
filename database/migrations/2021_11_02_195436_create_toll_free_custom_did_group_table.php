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
        Schema::create('toll_free_custom_did_group', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('toll_free_did_id');
            $table->unsignedBigInteger('custom_did_group_id');
            $table->unsignedInteger('company_id');

            $table->foreign('toll_free_did_id')
                ->references('id')
                ->on('profile_did_toll_frees')
                ->onDelete('cascade');

            $table->foreign('custom_did_group_id')
                ->references('id')
                ->on('custom_did_groups')
                ->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('toll_free_custom_did_group');
    }
};
