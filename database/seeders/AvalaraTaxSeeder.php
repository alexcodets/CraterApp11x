<?php

namespace Database\Seeders;

use Crater\Models\Address;
use Crater\Models\AvalaraConfig;
use Crater\Models\Company;
use Crater\Models\Country;
use Crater\Models\Currency;
use Crater\Models\Item;
use Crater\Models\PbxPackages;
use Crater\Models\State;
use Crater\Models\User;
use Crater\Models\UserSetting;
use Illuminate\Database\Seeder;
use Storage;

class AvalaraTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);

        if (User::where('email', $clientData[0]['client']['email'])->count() > 0) {
            var_dump('Client already exist');

            return;
        }

        if (Company::count() == 0) {
            $company = Company::factory()->create();
        }

        $company = Company::first();

        $userData = $clientData[0]['client'];

        $userData = array_merge(
            $userData,
            ['company_id' => $company->id, 'creator_id' => User::first()->id ?? null, 'currency_id' => Currency::first()->id ?? null]
        );
        //password = ThePassword
        $user = User::create($userData);

        $addressExtraData = ['user_id' => $user->id, 'company_id' => $user->company_id];
        $country = Country::where('code', '=', 'US')->first();

        if (is_null($country)) {
            $country = Country::create([
                'code' => 'US',
                'name' => 'United States',
                'phonecode' => 1,
            ]);
            $state = State::create([
                'country_alpha2' => 'US',
                'code' => 'NC',
                'name' => 'North Carolina'
            ]);
            $addressExtraData['country_id'] = $country->id;
            $addressExtraData['state_id'] = $state->id;
        }

        Address::create(array_merge($clientData[0]['address']['shipping'], $addressExtraData));
        Address::create(array_merge($clientData[0]['address']['billing'], $addressExtraData));

        UserSetting::create([
            'key' => 'language',
            'value' => 'en',
            'user_id' => $user->id,
        ]);

        #Items
        $items = json_decode(Storage::disk('seed')->get('items.json'), true);
        foreach ($items as $item) {
            $item['company_id'] = $user->company_id;
        }
        Item::insert($items);

        AvalaraConfig::factory()->create([
            'item_did_id' => Item::theType(19, 21)->first()->id,
            'item_cdr_id' => Item::theType(19, 48)->first()->id,
            'item_extension_id' => Item::theType(19, 41)->first()->id,
            'item_international_id' => Item::theType(19, 48)->first()->id,
            'company_id' => $company->id,
        ]);

        PbxPackages::where('id', 1)->update([
            'avalara_did_item_id' => Item::theType(19, 21)->first()->id, // Lines
            'cdr_items_id' => Item::theType(19, 48)->first()->id, // 	Wireless Access Charge
            'avalara_extension_item_id' => Item::theType(19, 41)->first()->id, // 	PBX Extension
            'custom_destinations_item_id' => Item::theType(19, 50)->first()->id, // 	Intrastate Usage
            'avalara_custom_app_rate_item_id' => Item::theType(19, 8)->first()->id,// install
            //seleccionar unos luego.
            'inter_custom_destinations_item_id' => Item::theType(19, 51)->first()->id, // 	International Usage
            'toll_free_custom_destinations_item_id' => Item::theType(19, 635)->first()->id, // toll free
            'avalara_services_price_item_id' => Item::theType(19, 37)->first()->id//	Equipment Rental
        ]);

        //Wrong User Data
        AvalaraConfig::factory([
            'user_name' => 'daName',
            'password' => 'PaSwOoRd',
            'client_id' => $user->id,
            'conexion' => 'TheConexion UserWorng',
            'status' => 'A',
        ])->create([
            'item_did_id' => Item::theType(19, 21)->first()->id,
            'item_cdr_id' => Item::theType(19, 48)->first()->id,
            'item_extension_id' => Item::theType(19, 41)->first()->id,
            'item_international_id' => Item::theType(19, 48)->first()->id,
        ]);

        //Wrong User Data
        AvalaraConfig::factory([
            'user_name' => 'daName2',
            'password' => 'PaSwOoRd',
            'client_id' => $user->id,
            'url' => 'www.mundop.test',
            'host' => '0900',
            'conexion' => 'TheConexion HostWorng2',
        ])->create([
            'item_did_id' => Item::theType(19, 21)->first()->id,
            'item_cdr_id' => Item::theType(19, 48)->first()->id,
            'item_extension_id' => Item::theType(19, 41)->first()->id,
            'item_international_id' => Item::theType(19, 48)->first()->id,
        ]);


    }
}
