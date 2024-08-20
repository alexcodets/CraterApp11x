<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('aux_vaults', function (Blueprint $table) {
            $table->after('cvv', function (Blueprint $table) {
                $table->string('ach_routing_number', 4)->nullable();
                $table->string('ach_account_number', 4)->nullable();
            });
            $table->string('card_number', 4)->nullable()->change();
            $table->string('expiry_date', 4)->nullable()->change();

        });
    }
};
