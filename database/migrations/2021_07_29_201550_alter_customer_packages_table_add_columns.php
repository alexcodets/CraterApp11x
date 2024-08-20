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
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->enum('tax_by', ['N', 'G', 'I'])->after('company_id');
            $table->boolean('allow_discount')->after('tax_by');
            $table->enum('discount_type', ['fixed', 'percentage'])->after('discount_by');
            $table->decimal('discount', 15, 2)->after('discount_type');
            $table->unsignedBigInteger('discount_val')->after('discount');
            $table->unsignedBigInteger('sub_total')->after('discount_val');
            $table->unsignedBigInteger('total')->after('sub_total');
            $table->unsignedBigInteger('tax')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('customer_packages', function (Blueprint $table) {
            $table->dropColumn([
                'tax_by',
                'allow_discount',
                'discount_type',
                'discount',
                'discount_val',
                'sub_total',
                'total',
                'tax'
            ]);
        });
    }
};
