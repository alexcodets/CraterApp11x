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
        Schema::table('avalara_invoices', function (Blueprint $table) {
            $table->unsignedTinyInteger('status')->default(1)->comment('0 => draft, 1 => active, 2 => void, 3 => reversed, 4 => edited')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('avalara_invoices', function (Blueprint $table) {
            $table->removeColumn('status');
        });
    }
};
