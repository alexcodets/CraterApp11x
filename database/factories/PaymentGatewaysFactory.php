<?php

namespace Database\Factories;

use Crater\Models\PaymentGateways;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentGatewaysFactory extends Factory
{
    protected $model = PaymentGateways::class;

    public function definition(): array
    {
        return [
            'status' => 'A',
            'url_img' => $this->faker->word(),
            'description' => $this->faker->paragraph(),
        ];
    }
}
