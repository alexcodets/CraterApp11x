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
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('retentions_bool')->default(false)->after('avalara_payment_type');
            $table->unsignedBigInteger('retentions_id')->nullable()->after('retentions_bool');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('retentions_bool');
            $table->dropColumn('retentions_id');
        });
    }
};
