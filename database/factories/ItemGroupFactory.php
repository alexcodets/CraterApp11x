<?php

namespace Database\Factories;

use Crater\Models\ItemGroup;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ItemGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence($nbWords = 3, $variableNbWords = true),
            'description' => $this->faker->text($maxNbChars = 200),
            'company_id' => User::where('role', 'super admin')->first()->company_id
        ];
    }
}
