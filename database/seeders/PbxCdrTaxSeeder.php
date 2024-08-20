<?php

namespace Database\Seeders;

use Crater\Models\PbxServices;
use Crater\Models\PbxServicesTaxTypes;
use Crater\Models\PbxServicesTaxTypesCdr;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class PbxCdrTaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {

        $this->generateCdrTax();
        //$this->generateServiceTax();

        User::where('id', 2)->update([
            'status_payment' => 'prepaid',
            'balance' => 123.75,
        ]);

    }

    private function generateCdrTax()
    {
        $user = User::first();
        $service = PbxServices::first();

        PbxServicesTaxTypesCdr::create([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'pbx_services_id' => $service->id,
            //'tax_types_id'    => 1,
            'name' => 'tax 1 CDR',
            'percent' => 2.25,
            'compound_tax' => 0,
            'status' => 'A',
        ]);

        PbxServicesTaxTypesCdr::create([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'pbx_services_id' => $service->id,
            //'tax_types_id'    => 1,
            'name' => 'tax 2 CDR',
            'percent' => 1.25,
            'compound_tax' => 0,
            'status' => 'A',
        ]);

        PbxServicesTaxTypesCdr::create([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'pbx_services_id' => $service->id,
            //'tax_types_id'    => 1,
            'name' => 'tax Compound CDR',
            'percent' => 1.75,
            'compound_tax' => 1,
            'status' => 'A',
        ]);
    }

    private function generateServiceTax()
    {
        PbxServicesTaxTypes::create([
            'company_id' => 1,
            'creator_id' => 2,
            'pbx_services_id' => 1,
            //'tax_types_id'    => 1,
            'name' => 'tax Basic CDR',
            'percent' => 1.83,
            'compound_tax' => 0,
            'status' => 'A',
        ]);

        PbxServicesTaxTypes::create([
            'company_id' => 1,
            'creator_id' => 2,
            'pbx_services_id' => 1,
            //'tax_types_id'    => 1,
            'name' => 'tax Basic CDR',
            'percent' => 0.17,
            'compound_tax' => 0,
            'status' => 'A',
        ]);
    }
}
