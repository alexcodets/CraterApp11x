<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            CurrenciesTableSeeder::class,
            DefaultSettingsSeeder::class,
            CountriesTableSeeder::class,
            PaymentMethodSeeder::class,
            UnitSeeder::class,
            StatesSeeder::class,
            PbxConfPrefix::class,
        ]);

        if (config('app.env') == 'local' || config('app.env') == 'debug' || config('app.env') == 'testing') {
            $this->call([
                ModulesSeeder::class,
                AvalaraServiceTypesSeeder::class,
                InvoiceTemplateSeeder::class,
                EstimateTemplateSeeder::class,
                PbxPackageServerSeeder::class,
                AvalaraTaxSeeder::class,
                CustomerPackageSeeder::class,
                PbxDidAndExtSeeder::class,
                InternationalRateSeeder::class,
                DemoSeeder::class,
            ]);

//            $this->call([
//                PbxServicesSeeder::class,
//                PbxCDRSeeder::class,
//                PbxCdrTotalesSeeder::class,
//                AvalaraLocationSeeder::class,
//                AvalaraExemptionSeeder::class,
//                AvalaraInvoiceSeeder::class,
//                //PbxHistoryCallIndiSeeder::class,
//                PbxCdrTaxSeeder::class,
//                //AvalaraExem
//                //BandwidthSeeder::class,
//                //PbxTenantCdrSeeder::class,
//                ExtStatusSendEmailSeeder::class,
//            ]);

        }

    }
}
