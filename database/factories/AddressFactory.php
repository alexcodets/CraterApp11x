<?php

namespace Database\Factories;

use Crater\Models\Address;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'address_street_1' => $this->faker->streetAddress(),
            'address_street_2' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            //'state' => $this->faker->state(),
            //'country_id'       => 231,
            'zip' => 231,//$this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
            'type' => $this->faker->randomElement([Address::BILLING_TYPE, Address::SHIPPING_TYPE]),
            'user_id' => User::factory()
        ];
    }

    public function billing(): object
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Address::BILLING_TYPE,
            ];
        });
    }

    public function shipping(): object
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => Address::SHIPPING_TYPE,
            ];
        });
    }
}
