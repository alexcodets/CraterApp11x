<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'country_alpha2' => str_random(2),
            'code' => str_random(3),
            'name' => $this->faker->state(),
        ];
    }
}
