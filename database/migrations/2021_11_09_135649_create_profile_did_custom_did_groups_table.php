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
        Schema::create('profile_did_custom_did_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('profile_did_id');
            $table->unsignedInteger('custom_did_group_id');

            /*   $table->foreign('profile_did_id')
                   ->references('id')
                   ->on('profile_did')
                   ->onDelete('cascade');

               $table->foreign('custom_did_group_id')
                   ->references('id')
                   ->on('custom_did_groups')
                   ->onDelete('cascade');*/

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
        Schema::dropIfExists('profile_did_custom_did_groups');
    }
};
