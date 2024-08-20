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
        Schema::table('invoice_dids', function (Blueprint $table) {
            $table->unsignedInteger('template_did_id')
                ->nullable()
                ->change();
            $table->string('template_did_name')
                ->nullable()
                ->change();
            $table->unsignedBigInteger('custom_did_id')
                ->after('template_did_rate')
                ->nullable();
            $table->decimal('custom_did_rate')
                ->default(0)
                ->after('custom_did_id')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('invoice_dids', function (Blueprint $table) {
            $table->dropColumn(['custom_did_id', 'custom_did_rate']);
        });
    }
};
