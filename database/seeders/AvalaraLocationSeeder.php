<?php

namespace Database\Seeders;

use Crater\Models\AvalaraLocation;
use Illuminate\Database\Seeder;

class AvalaraLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $address = [
            'country' => 'USA',
            'address' => '11 West 53 Street',
            'city' => 'Manhattan',
            'state' => 'NY',
            'zip' => '10019',
            'user_id' => 2,
            'company_id' => 1,
        ];
        //street_address
        AvalaraLocation::create(array_merge($address, ['geo' => true, 'type' => AvalaraLocation::ADDRESS]));
        //AvalaraLocation::FIPS
        AvalaraLocation::create(array_merge($address, ['geo' => true, 'type' => AvalaraLocation::GEO]));
        //FIPS
        AvalaraLocation::create([
            'type' => AvalaraLocation::FIPS,
            'fips' => '9902604301',
            'user_id' => 2,
            'company_id' => 1,
        ]);
        //NPANXX
        AvalaraLocation::create([
            'type' => AvalaraLocation::NPANXX,
            'npa' => '212200',
            'user_id' => 2,
            'company_id' => 1,
        ]);
        //PCODE
        AvalaraLocation::create([
            'type' => AvalaraLocation::PCODE,
            'pcd' => '2604301',
            'user_id' => 2,
            'company_id' => 1,
        ]);

        //$user->location()->attach(AvalaraLocation::find(3));
        //(User::find(2))->location()->sync([1]);

        //User::where('id', 2)->update(['avalara_location_id' => 1]);
        //(Company::find(1))->locations()->attach(AvalaraLocation::find(3));

    }
}
