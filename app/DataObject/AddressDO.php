<?php

namespace Crater\DataObject;

use Crater\Models\Address;

class AddressDO
{
    public static function getAddress()
    {
        //It will work even if there is not address, and there could be used default values for certain fields
        // Like NA 'No Set', etc.
        try {
            $add = Address::whereNULL("user_id")
                ->join("countries", "countries.id", "=", "addresses.country_id")
                ->join("states", "states.id", "=", "addresses.state_id")
                ->select([
                    "countries.name as country", "states.name as state", "states.code as state_code", "addresses.zip as zip",
                    "addresses.phone as phone", "addresses.city as city", "addresses.address_street_1 as address_street_1",
                    "addresses.address_street_2 as address_street_2"
                ])
                ->first();

            if ($add == null) {
                $add = Address::first()
                    ->join("countries", "countries.id", "=", "addresses.country_id")
                    ->join("states", "states.id", "=", "addresses.state_id")
                    ->select([
                        "countries.name as country", "states.name as state", "states.code as state_code",
                        "addresses.zip as zip", "addresses.phone as phone", "addresses.city as city",
                        "addresses.address_street_1 as address_street_1", "addresses.address_street_2 as address_street_2"
                    ])
                    ->first();
            }
        } catch (\Throwable $th) {
            /*$add = (object) [
                'country' => null, 'state' => null, 'state_code' => null, 'zip' => null,
                'phone' => null, 'city' => null, 'address_street_1' => null, 'address_street_2' => null
            ];*/

            $add = new Address([
                'country' => null, 'state' => null, 'state_code' => null, 'zip' => null,
                'phone' => null, 'city' => null, 'address_street_1' => null, 'address_street_2' => null
            ]);
        }

        return $add;

    }
}
