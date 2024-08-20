<?php

namespace Database\Factories;

use Crater\Models\AdditionalCharge;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceAdditionalChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'additional_charge_id' => AdditionalCharge::factory()->create()->id,
            'additional_charge_name' => $this->faker->name(),
            'additional_charge_amount' => 100,
            'template_name' => 'The Additional Template',
            'additional_charge_type' => 'Nose',
            'qty' => $this->faker->numberBetween(1, 275),
            'total' => 100,

        ];
    }
}
