<?php

namespace Database\Seeders;

use Crater\Models\AvalaraLocation;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class AvalaraExemptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $loc = AvalaraLocation::create([
            'pcd' => '2604301',
            'type' => AvalaraLocation::PCODE,
        ]);

        $user = User::find(2);

        $user->exemptions()->create([
            'tpe' => 0,
            'dom' => 1,
            'cat' => 2,
            'exnb' => false,
            'avalara_locations_id' => $loc->id,
            'pbx_services_id' => 1,
        ]);

        $user->exemptions()->create([
            'tpe' => 1,
            'dom' => 1,
            'cat' => 0,
            'exnb' => false,
            'avalara_locations_id' => 3,
            'pbx_services_id' => 1,
        ]);

    }
}
