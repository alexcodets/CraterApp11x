<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //
        DB::table('payment_gateways')->delete();
        $modules = [

            ['name' => 'Authorize', 'description' => 'Accept credit cards, contactless payments, and eChecks in person and on the go.', 'default' => 1,'url_img' => '/images/authorize-net-01.png','status' => 'A','slug' => null,'company_id' => 1,'creator_id' => 1],
            ['name' => 'Paypal', 'description' => 'Shopping online shouldnt cost you peace of mind. Buy from millions of online stores without sharing your financial information.', 'default' => 0,'url_img' => '/images/PayPal-Logo.jpg','status' => 'A','slug' => null,'company_id' => 1,'creator_id' => 1],
            ['name' => 'AuxVault', 'description' => 'Get paid anytime, anywhere with AuxVAULT, our 100% Level 1 PCI compliant cloud-based payment gateway.', 'default' => 0,'url_img' => '/images/auxpay.png','status' => 'A','slug' => null,'company_id' => 1,'creator_id' => 1],
            ['name' => 'Stripe', 'description' => 'Online payment processing for internet businesses. Stripe is a suite of payment APIs that powers commerce for businesses of all sizes.', 'default' => 0,'url_img' => '/images/stripe.png','status' => 'A','slug' => null,'company_id' => 1,'creator_id' => 1],
        ];

        $id = 0;

        foreach ($modules as $module) {
            $id++;
            $module['id'] = $id;
            DB::table('payment_gateways')->insert($module);
        }
    }
}
