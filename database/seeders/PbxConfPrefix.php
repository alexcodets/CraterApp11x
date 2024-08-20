<?php

namespace Database\Seeders;

use Crater\Models\CompanySetting;
use Illuminate\Database\Seeder;

class PbxConfPrefix extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        CompanySetting::create(['option' => 'packages_pbx_prefix','value' => 'PBXP', 'company_id' => 1]);
        CompanySetting::create(['option' => 'extension_pbx_prefix','value' => 'PBXE', 'company_id' => 1]);
        CompanySetting::create(['option' => 'did_pbx_prefix','value' => 'PBXD', 'company_id' => 1]);
        CompanySetting::create(['option' => 'pbx_services_prefix','value' => 'SERVI', 'company_id' => 1]);
    }
}
