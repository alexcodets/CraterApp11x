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

            $table->enum('status_customer', ['A', 'I', 'F'])->default('A')->after('email');

        });

        if (Schema::hasColumn('users', 'deleted_at') == false) {

            Schema::table('users', function (Blueprint $table) {

                $table->softDeletes();
            });

        }
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
