<?php

namespace Database\Seeders;

use Crater\Models\Address;
use Crater\Models\Country;
use Crater\Models\Setting;
use Crater\Models\State;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::where('role', 'super admin')->first();
        $user->setSettings(['language' => 'en']);

        Address::create(['company_id' => $user->company_id, 'country_id' => Country::first()->id,
                         'state_id' => State::first()->id]);

        Setting::setSetting('profile_complete', 'COMPLETED');
        if (\Storage::disk('local')->has('database_created') === false) {
            \Storage::disk('local')->put('database_created', 'database_created');
        }
    }
}
