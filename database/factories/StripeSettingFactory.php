<?php

namespace Database\Factories;

use Crater\Models\StripeSetting;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class StripeSettingFactory extends Factory
{
    protected $model = StripeSetting::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'public_key' => $this->faker->word(),
            'secret_key' => $this->faker->word(),
            'status' => 'A',
            'currency' => 'USD',
            'environment' => 'sandbox',
            'creator_id' => User::factory(),
        ];
    }
}
