<?php

namespace Database\Factories;

use Crater\Models\PbxServices;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxServicesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PbxServices::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'creator_id' => 1,
            'pbx_package_id' => 1,
            'pbx_tenant_id' => 1,
            'status' => 'A',
            'term' => PbxServices::TERM_MONTHLY,
            'date_begin' => now(),
            'allow_discount' => 0,
            'allow_discount_value' => 1,
            'date_from' => now(),
            'date_to' => null,
            'time_period' => 1,
            'time_period_value' => 'Months',
            'customer_id' => 1,

        ];
    }
}
