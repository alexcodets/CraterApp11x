<?php

namespace Database\Seeders;

use Crater\Models\PbxPackages;
use Crater\Models\PbxServers;
use Crater\Models\PbxTenant;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class PbxPackageServerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        $selected = config('pbxware.selected');
        //$selected = 'dev';
        $host = config("pbxware.{$selected}.base_url");
        $apiKey = config("pbxware.{$selected}.private_key");
        $port = 443;

        $user = User::first();

        $server = PbxServers::create([
            'server_label' => 'My Second Server',
            'hostname' => $host,
            'ssl_port' => $port,
            'api_key' => $apiKey,
            'national_dialing_code' => '1',
            'international_dialing_code' => '+1',
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
        ]);

        PbxPackages::create([
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
            'pbx_server_id' => $server->id,
            'rate' => 21.68,
            'type_time_increment' => 'sec',
            'minutes_increments' => 17,
            'rate_per_minutes' => 0.15,
            'packages_number' => 5,
            'inclusive_minutes' => 1445,
            'inclusive_minutes_seconds' => 86700,
            'avalara_options' => 1,
            'all_cdrs' => 0,
            'avalara_extension' => 1,
            'avalara_did' => 1,
            'avalara_callrating' => 1,
            'avalara_items' => 1,
            'avalaraBundle' => false,
            'bundleTransaction' => 1325,
            'bundleService' => 1326,
            'avalara_services_price_item_id' => 1,
            'avalara_services_price_item' => true,
            'avalara_additional_charges_item' => true,
            'avalara_additional_charges_item_id' => 1,
        ]);

        PbxTenant::create([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'name' => config("pbxware.{$selected}.tenant.name"),
            'code' => config("pbxware.{$selected}.tenant.code"),
            'tenantid' => config("pbxware.{$selected}.tenant.tenantid"),
            'details' => json_encode(config("pbxware.{$selected}.tenant.details")),
            'pbx_server_id' => $server->id,
        ]);

        PbxTenant::create([
            "name" => "PepeGanga",
            "code" => "333",
            "tenantid" => 50,
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
        ]);

        PbxTenant::create([
            "name" => "Acme Corporation",
            "code" => "820",
            "tenantid" => 49,
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
        ]);

    }
}
