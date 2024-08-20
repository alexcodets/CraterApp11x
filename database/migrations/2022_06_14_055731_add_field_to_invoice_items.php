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
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->unsignedBigInteger('retention_id')->nullable()->after('item_id');
            $table->string('retention_concept')->nullable()->after('retention_id');
            $table->unsignedBigInteger('retention_percentage')->nullable()->after('retention_concept');
            $table->unsignedBigInteger('retention_amount')->default(0)->after('retention_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropColumn('retention_id');
            $table->dropColumn('retention_amount');
            $table->dropColumn('retention_concept');
            $table->dropColumn('retention_percentage');
        });
    }
};
