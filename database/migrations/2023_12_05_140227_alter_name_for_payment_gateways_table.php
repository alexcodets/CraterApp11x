<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            DB::statement("ALTER TABLE payment_gateways MODIFY COLUMN name ENUM('Authorize','Paypal','AuxVault')");
        });
    }
};
