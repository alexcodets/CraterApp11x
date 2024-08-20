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
        Schema::table('addresses', function (Blueprint $table) {
            $table->boolean('tax_exempt')->after('type')->nullable();
            $table->string('tax_id_vatin', 160)->after('tax_exempt')->nullable();
            $table->enum('delivery_method', ['Email', 'Paper', 'InterFax', 'PostalMethods'])->after('tax_exempt')->nullable();
            $table->boolean('payment_notices')->after('delivery_method')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            //
        });
    }
};
