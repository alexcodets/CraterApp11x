<?php

namespace Database\Factories;

use Crater\Models\AvalaraServiceType;
use Crater\Models\Item;
use Crater\Models\Unit;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'description' => $this->faker->text(),
            'company_id' => User::where('role', 'super admin')->first()->company_id ?? null,
            'price' => $this->faker->randomDigitNotNull(),
            'unit_id' => Unit::factory()
        ];
    }

    public function avalara()
    {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->name(),
                'description' => $this->faker->text(),
                //'company_id'  => User::where('role', 'super admin')->first()->company_id ?? null,
                'price' => $this->faker->randomDigitNotNull(),
                'unit_id' => Unit::factory(['company_id' => null]),
                'avalara_bool' => 1,
                'avalara_payment_type' => 'TAXABLE_AMOUNT',
                'avalara_service_type' => AvalaraServiceType::factory(),
                'avalara_type' => $this->faker->randomElement(['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '13', '16', '18', '19', '20', '21', '59', '65']),
            ];
        });
    }

    public function avalaraValid()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Invoice::STATUS_PAID,
            ];
        });
    }
}
