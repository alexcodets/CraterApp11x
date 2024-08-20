<?php

namespace Database\Factories;

use Crater\Models\AuxVault;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class AuxVaultFactory extends Factory
{
    protected $model = AuxVault::class;

    public function definition(): array
    {
        return [
            'transaction_id' => $this->faker->randomNumber(20, true),
            'base_amount' => $this->faker->randomFloat(),
            'amount' => $this->faker->randomFloat(),
            'card_number' => $this->faker->randomNumber(4, true),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'city' => $this->faker->city(),
            'state' => $this->faker->word(),
            'postal_code' => $this->faker->postcode(),
            'country' => $this->faker->country(),
            'country_code' => $this->faker->word(),
            'phone_number' => $this->faker->phoneNumber(),
            'expiry_date' => $this->faker->randomNumber(4, true),
            'cvv' => $this->faker->word(),
            'transaction_type' => $this->faker->randomNumber(2),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
