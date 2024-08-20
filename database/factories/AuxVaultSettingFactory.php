<?php

namespace Database\Factories;

use Crater\Models\AuxVaultSetting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AuxVaultSettingFactory extends Factory
{
    protected $model = AuxVaultSetting::class;

    public function definition(): array
    {
        return [
            'endpoint' => $this->faker->word(),
            'api_key' => $this->faker->word(),
            'merchant_id' => $this->faker->word(),
            'currency' => $this->faker->word(),
            'default' => $this->faker->boolean(),
            'production' => $this->faker->boolean(),
            'user_id' => $this->faker->randomNumber(),
            'company_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
