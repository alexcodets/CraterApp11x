<?php

namespace Database\Seeders;

use Crater\Models\BandwidthAccount;
use Illuminate\Database\Seeder;

class BandwidthSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        BandwidthAccount::create([
            'name' => 'Default Account',
            'user_name' => env('BANDWIDTH_USER_NAME'),
            'password' => env('BANDWIDTH_PASSWORD'),
            'accountid' => env('BANDWIDTH_ACCOUNTID'),
            'url' => env('BANDWIDTH_URL'),
            'enable' => true,
            'selected' => true,
        ]);
    }
}
