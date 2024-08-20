<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AdditionalChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            // 'profile_extension_id' => 1,
            'description' => $this->faker->text(75),
            'amount' => 1.00,
            'status' => 1,
        ];
    }
}
