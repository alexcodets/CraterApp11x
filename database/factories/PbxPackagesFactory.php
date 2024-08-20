<?php

namespace Database\Factories;

use Crater\Models\PbxPackages;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxPackagesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PbxPackages::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'pbx_package_name' => 'My first Pbx Package',
            'html' => '',
            'text' => '',
            'status' => 'A',
            'qty_available' => 7650,
            'client_limit' => 715,
            'extensions' => 1,
            'did' => 1,
            'call_ratings' => 1,
            'package_discount' => 0,
            'template_did_id' => 1,
            'template_extension_id' => 1,
            //Que es este type
            'type' => 'min',
            'discount' => 0,
            'modify_server' => 0,
            'pbx_server_id' => null,
            'rate' => 0.01,
            'type_time_increment' => 'sec',
            'minutes_increments' => 17,
            'rate_per_minutes' => 15,
            'packages_number' => 5,
        ];
    }
}
