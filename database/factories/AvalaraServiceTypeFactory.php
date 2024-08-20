<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AvalaraServiceTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_type' => $this->faker->numberBetween(3, 77),
            'avalara_transaction_types' => $this->faker->numberBetween(3, 77),
            'service_type_name' => $this->faker->name(),
            'taxable_amount' => $this->faker->numberBetween(1, 23),
            'lines' => $this->faker->boolean(),
        ];
    }
}
