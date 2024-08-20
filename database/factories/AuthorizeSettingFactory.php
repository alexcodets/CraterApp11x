<?php

namespace Database\Factories;

use Crater\Authorize\Models\AuthorizeSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class AuthorizeSettingFactory extends Factory
{
    protected $model = AuthorizeSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'status' => 'A',
            'login_id' => config('seeder.payments.authorize.login_id'),
            'transaction_key' => config('seeder.payments.authorize.transaction_key'),
            'payment_API' => $this->faker->randomElement(['CIM', 'AIM']),
            'payment_account_validation_mode' => 'test',
            'test_mode' => 1,
            'developer_mode' => 1,
            'currency' => 'USD',
            'is_default' => 1,
        ];
    }
}
