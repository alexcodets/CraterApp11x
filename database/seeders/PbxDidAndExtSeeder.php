<?php

namespace Database\Seeders;

use Crater\Models\Company;
use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServers;
use Crater\Models\PbxTenant;
use Crater\Models\ProfileDID;
use Crater\Models\ProfileExtensions;
use Crater\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class PbxDidAndExtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $tenant = PbxTenant::first();
        $dids = json_decode(Storage::disk('seed')->get('dids.json'), true);
        $extensions = json_decode(Storage::disk('seed')->get('extensions.json'), true);
        $server = PbxServers::first();
        //did primero
        $user = User::first();
        $company = Company::first();
        $pbx_did = PbxDID::create([
            'server' => 49,
            'trunk' => 60,
            'type' => 'Queues',
            'ext' => '8001',
            'status' => 'enabled',
            'pbx_tenant_id' => $tenant->id,
            'company_id' => $company->id,
            'creator_id' => $user->id,
        ]);

        foreach ($dids as $did) {
            $pbx_did = PbxDID::create([
                'number' => $did['number'],
                'server' => $did['server'],
                'status' => $did['status'],
                'pbx_tenant_id' => $tenant->id,
                'company_id' => $company->id,
                'creator_id' => $user->id,
            ]);

            ProfileDID::create([
                'name' => "Did profile for did: {$pbx_did->number}",
                'description' => '<p>Just a Example DID</p>',
                'status' => 'A',
                'did_rate' => rand(1, 100),
                'did_number' => $pbx_did->number,
                'inbound_per_minute_rate' => rand(1, 100),
                'inbound_per_minute_rate_value' => rand(1, 100),
                'company_id' => $company->id,
                'creator_id' => $user->id,
            ]);
        }

        $pbxExtension = PbxExtensions::create([
            'name' => "Peter Griffin",
            'ext' => 2001,
            'email' => "none@none.com",
            'status' => "enabled",
            'pbx_tenant_id' => $tenant->id,
            'protocol' => "sip",
            'location' => "location",
            'ua_id' => "50",
            'company_id' => $company->id,
            'creator_id' => $user->id,
            "pbx_tenant_code" => 42,
            'pbxext_id' => 3,
            'extensionid' => 3,
            'pbx_server_id' => $server->id,
        ]);

        foreach ($extensions as $ext) {
            $pbxExtension = PbxExtensions::create([
                'name' => $ext['name'],
                'ext' => $ext['ext'],
                'email' => $ext['email'],
                'status' => $ext['status'],
                'pbx_tenant_id' => $tenant->id,
                'company_id' => $company->id,
                'creator_id' => $user->id,
            ]);

            ProfileExtensions::create([
                'name' => "Extension profile for Extension: {$pbxExtension->name}",
                'description' => '<p>Just a Example DID</p>',
                'status' => 'A',
                'rate' => rand(1, 50) / 100,
                'inbound_per_minute_rate' => rand(1, 50),
                'outbound_per_minute_rate' => rand(1, 50),
                'extensions_number' => $pbxExtension->ext,
                'minutes_increments' => rand(3, 20),
                'company_id' => $company->id,
                'creator_id' => $user->id,
            ]);
        }

    }
}
