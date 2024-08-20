<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ContactsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->email(),
            'name' => $this->faker->firstName(),
            'phone' => $this->faker->phoneNumber(),
            'type' => $this->faker->randomElement(['B','S','R']),
            'allow_receive_emails' => 1,
            'email_payments' => 1,
        ];
    }
}
