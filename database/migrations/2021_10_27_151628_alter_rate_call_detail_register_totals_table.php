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
        Schema::table('call_detail_register_totals', function (Blueprint $table) {
            DB::statement("ALTER TABLE call_detail_register_totals MODIFY rate DECIMAL(20,5)");
            $table->unsignedBigInteger('international_rate_id')->nullable()->after('pbx_extension_id');
            $table->foreign('international_rate_id')->references('id')->on('international_rate')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE call_detail_register_totals MODIFY rate INT");
    }
};
