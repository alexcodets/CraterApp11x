<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('failed_payment_history', function (Blueprint $table) {
            $table->string('type', 40)->default('payment')->after('transaction_number');
        });
    }
};
