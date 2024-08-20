<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'country_id' => 1,
            'state_id' => 1,
            'city' => 'Alabama',
            'address_1' => 'Elm Street',
            'zip' => $this->faker->postcode(),
            'card_number' => \Crypt::encryptString($this->faker->creditCardNumber()),
            'credit_card' => \Crypt::encryptString($this->faker->creditCardNumber()),
            'payment_account_type' => $this->faker->randomElement(['CC', 'ACH']),
            'status' => 'A',
            'client_id' => 1,
            'company_id' => 1,
            'main_account' => 1,
            'expiration_date' => \Crypt::encryptString(now()->addMonths(3)->format('Y-m')),
        ];
    }
}
