<?php

namespace Database\Factories;

use Crater\Models\AvalaraLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvalaraLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'type' => AvalaraLocation::PCODE,
            'pcd' => '2604301',
        ];
    }
}
