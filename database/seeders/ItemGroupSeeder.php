<?php

namespace Database\Seeders;

use Crater\Models\ItemGroup;
use Illuminate\Database\Seeder;

class ItemGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        ItemGroup::factory()->count(20)->create();
    }
}
