<?php

namespace Database\Factories;

use Crater\Models\CallHistoryIndi;
use Illuminate\Database\Eloquent\Factories\Factory;

class CallHistoryIndiFactory extends Factory
{
    protected $model = CallHistoryIndi::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'call_detail_register_totals_id' => $this->faker->numberBetween(1, 38),
            'amout' => $amount = $this->faker->randomFloat(5, 0.02500, 200.00001),
            'taxamount' => $taxamount = $this->faker->randomFloat(5, 0.02500, 7.00001),
            'type' => 1,
            'percent' => $this->faker->randomFloat(5, 0.02500, 14.00000),
            'amoutbruto' => $amount + $taxamount,
        ];
    }
}
