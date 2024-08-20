<?php

namespace Database\Seeders;

use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxPackages;
use Crater\Models\PbxServicePrefixRateGroup;
use Crater\Models\PbxServices;
use Crater\Models\PbxServicesDID;
use Crater\Models\PbxServicesExtensions;
use Crater\Models\PbxTenant;
use Crater\Models\PrefixrateGroup;
use Crater\Models\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Storage;

class PbxServicesSeeder extends Seeder
{
    /**
     * @throws FileNotFoundException
     */
    public function run(): void
    {
        $tenant = PbxTenant::first();
        $package = PbxPackages::first();
        $dids = PbxDID::all();
        $extensions = PbxExtensions::all();
        $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);
        $user = User::where('email', $clientData[0]['client']['email'])->first();

        $service = PbxServices::create([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'pbx_package_id' => $package->id,
            'pbx_tenant_id' => $tenant->id,
            'status' => 'A',
            'term' => 'monthly',
            'allow_discount' => 0,
            'allow_discount_value' => 1,
            'date_from' => now(),
            'date_begin' => now()->subDays(2),
            'date_to' => null,
            'time_period' => 1,
            'time_period_value' => 'Months',
            'inclusive_minutes_seconds_consumed' => 86700,
            'customer_id' => $user->id,
            'only_callrating' => 0,
            'prefixrate_groups_id' => 1,
            'prefixrate_groups_outbound_id' => 2,
            'cap_extension' => 120,
            'cap_total' => 120 * 60,
            'allow_aditionalcharges' => true,
            'main_update' => true,
        ]);

        $inbound = PrefixrateGroup::where('type', PbxServicePrefixRateGroup::TYPE_INBOUND)->first('id')->id;
        $outbound = PrefixrateGroup::where('type', PbxServicePrefixRateGroup::TYPE_OUTBOUND)->first('id')->id;

        $service->customGroups()->attach($inbound, ['type' => PbxServicePrefixRateGroup::TYPE_INBOUND]);
        $service->customGroups()->attach($outbound, [ 'type' => PbxServicePrefixRateGroup::TYPE_OUTBOUND]);

        //$user->account()->associate($account);
        //$user->roles()->attach($roleId, ['expires' => $expires]);


        foreach ($dids as $did) {
            PbxServicesDID::create([
                'company_id' => $user->company_id,
                'creator_id' => $user->id,
                'pbx_service_id' => $service->id,
                'pbx_did_id' => $did->id,
            ]);
        }

        foreach ($extensions as $ext) {
            PbxServicesExtensions::create([
                'company_id' => $user->company_id,
                'creator_id' => $user->id,
                'pbx_service_id' => $service->id,
                'pbx_extension_id' => $ext->id,
            ]);
        }

    }
}
