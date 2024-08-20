<?php

namespace Database\Factories;

use Crater\Models\PbxServicesTaxTypes;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxServicesTaxTypesFactory extends Factory
{
    protected $model = PbxServicesTaxTypes::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'percent' => 4,
            'compound_tax' => 0,
            'status' => 'A',
        ];
    }
}
