<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PbxServicesAppRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'app_name' => $this->faker->name(),
            'quantity' => $this->faker->numberBetween(1, 15),
            'costo' => $this->faker->numberBetween(1, 225),
        ];
    }
}
