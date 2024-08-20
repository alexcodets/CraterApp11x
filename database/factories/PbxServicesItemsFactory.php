<?php

namespace Database\Factories;

use Crater\Models\ItemGroup;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesItems;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxServicesItemsFactory extends Factory
{
    protected $model = PbxServicesItems::class;

    public function definition(): array
    {
        return [
            'pbx_services_id' => PbxServices::first()->id,
            'item_group_id' => ItemGroup::first()->id,
            'discount_type' => 'none', //Not idea
            'quantity' => 1,
            'price' => 1,
            'tax' => 0,
            'total' => 1,
            'status' => 'A',
        ];
    }
}
