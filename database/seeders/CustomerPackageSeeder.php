<?php

namespace Database\Seeders;

use Crater\Models\CustomerPackage;
use Crater\Models\Packages;
use Crater\Models\User;
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
        $customer = User::find(2) ?? User::factory()->create();
        $user = User::first();

        $package = Packages::create([
            'name' => 'Mame',
        ]);

        CustomerPackage::create([
            'customer_id'             => $customer->id,
            'package_id'              => $package->id,
            'creator_id'              => $user->id,
            'company_id'              => $user->company_id,
            'tax_by'                  => 'N',
            'allow_discount'          => 0,
            'discount_type'           => 'percentage',
            'discount_by'             => 'Discount discount',
            'discount'                => 0,
            'discount_val'            => 0,
            'sub_total'               => 0,
            'total'                   => 25,
            'tax'                     => 0,
            'status'                  => 'A',
            'term'                    => 'monthly',
            'service_auto_suspension' => 0,
        ]);
    }
}
