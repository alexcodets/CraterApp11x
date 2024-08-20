<?php

namespace Database\Seeders;

use Crater\Models\CustomerPackage;
use Crater\Models\Packages;
use Illuminate\Database\Seeder;

class CustomerPackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $customer_id = 2;

        Packages::create([
            'name' => 'Mame',
        ]);

        CustomerPackage::create([
            'customer_id' => $customer_id,
            'package_id' => 1,
            'creator_id' => 1,
            'company_id' => 1,
            'tax_by' => 'N',
            'allow_discount' => 0,
            'discount_type' => 'percentage',
            'discount_by' => 'Discount discount',
            'discount' => 0,
            'discount_val' => 0,
            'sub_total' => 0,
            'total' => 25,
            'tax' => 0,
            'status' => 'A',
            'term' => 'monthly',
            'service_auto_suspension' => 0,
        ]);
    }
}
