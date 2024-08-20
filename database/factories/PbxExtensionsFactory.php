<?php

namespace Database\Factories;

use Crater\Models\PbxExtensions;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxExtensionsFactory extends Factory
{
    protected $model = PbxExtensions::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'email' => $this->faker->companyEmail(),
            'ext' => $this->faker->numberBetween(1000, 9999),
            'protocol' => 'sip',
            'location' => 'local',
            'ua_id' => '50',
            'ua_name' => 'generic_sip',
            'ua_fullname' => 'Generic SIP',
            'status' => 'disabled',
            'macaddress' => '',
            'linenum' => '',
            'pbxext_id' => $this->faker->numberBetween(1, 999)
        ];
    }
}
