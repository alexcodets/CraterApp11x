<?php

namespace Database\Seeders;

use Crater\Models\CustomSearch;
use Illuminate\Database\Seeder;

class CustomSearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //
        CustomSearch::create([
            'name' => 'One Custom',
            'description' => 'The first One',
            'pbx_tenant_id' => 1,
            'company_id' => 1,
        ]);

        CustomSearch::create([
            'name' => 'One Custom',
            'description' => 'The first One',
            'pbx_tenant_id' => 1,
            'company_id' => 1,
        ]);

        //CustomSearch::find(1)->pbxExtension()->attach([1,2,3,4]);
        //CustomSearch::find(2)->pbxExtension()->attach([5,6,7]);

        //


    }
}
