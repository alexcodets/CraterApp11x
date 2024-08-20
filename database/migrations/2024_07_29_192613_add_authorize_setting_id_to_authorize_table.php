<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('authorize', function (Blueprint $table) {
            Schema::table('aux_vaults', function (Blueprint $table) {
                $table->unsignedBigInteger('authorize_setting_id')->after('company_id')->nullable();
                // $table->decimal('fees')->after('amount')->nullable();
                //$table->foreign('aux_vault_setting_id')->references('id')->on('aux_vault_settings')->onDelete('cascade');
            });
        });
    }
};
