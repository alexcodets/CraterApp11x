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
        Schema::table('aditional_charges', function (Blueprint $table) {
            $table->unsignedInteger('profile_did_id')->nullable();
            $table->foreign('profile_did_id')->references('id')->on('profile_did')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('aditional_charges', function (Blueprint $table) {
            //
        });
    }
};
