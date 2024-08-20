<?php

namespace Database\Factories;

use Crater\Helpers\General;
use Crater\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Company::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'unique_hash' => General::generateUniqueId(),
            'name' => $this->faker->name(),
        ];
    }
}
