<?php

namespace Database\Factories;

use Crater\Models\PaymentMethod;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentMethodFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'company_id' => User::where('role', 'super admin')->first()->company_id ?? null,
        ];
    }
}
