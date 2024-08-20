<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('aux_vault_id')->nullable()->after('authorize_id');
            $table->foreign('aux_vault_id')->references('id')->on('aux_vaults');
        });
    }
};
