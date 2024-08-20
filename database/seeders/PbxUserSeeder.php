<?php

namespace Database\Seeders;

use Crater\Models\Company;
use Crater\Models\Currency;
use Crater\Models\User;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Seeder;
use Storage;

class PbxUserSeeder extends Seeder
{
    /**
     * @throws FileNotFoundException
     */
    public function run(): void
    {
        $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);

        if (User::where('email', $clientData[0]['client']['email'])->count() > 0) {
            return;
        }

        $company = Company::first();

        if (is_null($company)) {
            $company = Company::factory()->create();
        }

        $userData = $clientData[0]['client'];
        $userData = array_merge(
            $userData,
            ['company_id' => $company->id, 'creator_id' => User::first()->id ?? null, 'currency_id' => Currency::first()->id ?? null]
        );
        //password = ThePassword
        $user = User::create($userData);
    }
}
