<?php

namespace Database\Factories;

use Crater\Models\PaymentGatewaysFee;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentGatewaysFeeFactory extends Factory
{
    protected $model = PaymentGatewaysFee::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'type' => 'percentage', //percentage, fixed
            'amount' => $this->faker->numberBetween(0, 100),
            'payment_gateway' => 'Authorize', //'Authorize','Paypal','AuxVault','Stripe'
        ];
    }
}
