<?php

namespace Database\Factories;

use Crater\Models\PbxDID;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxDIDFactory extends Factory
{
    protected $model = PbxDID::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $num = $this->faker->numberBetween(11, 999);

        return [
            'number' => $this->faker->numberBetween(1111111111, 9999999999),
            'number2' => $this->faker->numberBetween(1111111111, 9999999999),
            'server' => '42',
            'trunk' => '60',
            'type' => '-',
            'ext' => $this->faker->numberBetween(1111, 9999),
            'e164' => null,
            'e164_2' => null,
            'status' => 'enabled',
            'didid' => $num,
            'pbxdid_id' => $num,
        ];
    }
}
