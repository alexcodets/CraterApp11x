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
        Schema::table('avalara_logs', function (Blueprint $table) {
            $table->after('status', function (Blueprint $table) {
                $table->unsignedTinyInteger('operation_type')->after('status')->default(0);
            });
            $table->after('user_id', function (Blueprint $table) {
                $table->unsignedBigInteger('avalara_log_id')->nullable();
                $table->string('note')->nullable();
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_logs', function (Blueprint $table) {
            $table->removeColumn('operation_type');
            $table->removeColumn('avalara_log_id');
            $table->removeColumn('note');

        });
    }
};
