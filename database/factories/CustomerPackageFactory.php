<?php

namespace Database\Factories;

use Crater\Models\Packages;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerPackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'package_id' => Packages::factory()->create()->id,
            'tax_by' => $this->faker->randomElement(['N','G','I']),
            'allow_discount' => 1,
            'discount_by' => '5%',
            'discount_type' => 'percentage',
            'discount' => 0.25,
            'total' => 10,
            'status' => 'A',
            'term' => 'monthly',
            'service_auto_suspension' => 1,
        ];
    }
}
