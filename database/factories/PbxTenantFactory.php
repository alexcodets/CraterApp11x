<?php

namespace Database\Factories;

use Crater\Models\PbxTenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxTenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = PbxTenant::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'code' => $this->faker->numberBetween(25, 999),
            'tenantid' => $this->faker->numberBetween(25, 999),
            'status' => 1,
        ];
    }
}
