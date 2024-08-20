<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaypalSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'email' => config('seeder.payments.paypal.email'),
            'paypal_id' => '',
            'paypal_secret' => config('seeder.payments.paypal.secret'),
            'paypal_signature' => '',
            'currency' => config('seeder.payments.paypal.currency'),
            'status' => 'A',
            'test_mode' => 1,
            'developer_mode' => '',
            'creator_id' => '',
            'merchant_id' => config('seeder.payments.paypal.merchant_id'),
            'public_key' => config('seeder.payments.paypal.public_key'),
            'private_key' => config('seeder.payments.paypal.private_key'),
            'enviroment' => 'sandbox',
        ];
    }
}
