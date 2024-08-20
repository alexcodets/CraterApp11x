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
        $this->call(UsersTableSeeder::class);
        $this->call(CurrenciesTableSeeder::class);
        $this->call(DefaultSettingsSeeder::class);
        $this->call(CountriesTableSeeder::class);

        //$this->call(EstimateTemplateSeeder::class);
        //$this->call(InvoiceTemplateSeeder::class);
        $this->call(PaymentMethodSeeder::class);
        $this->call(UnitSeeder::class);
        $this->call(StatesSeeder::class);
        $this->call(PbxConfPrefix::class);

        if (config('app.env') == 'local' || config('app.env') == 'debug') {
            $this->call([
                ModulesSeeder::class,
                AvalaraServiceTypesSeeder::class,
                InvoiceTemplateSeeder::class,
                PbxPackageServerSeeder::class,
                AvalaraTaxSeeder::class,
                CustomerPackageSeeder::class,
                PbxDidAndExtSeeder::class,
                InternationalRateSeeder::class,
                PbxServicesSeeder::class,
                PbxCDRSeeder::class,
                PbxCdrTotalesSeeder::class,
                AvalaraLocationSeeder::class,
                AvalaraExemptionSeeder::class,
                AvalaraInvoiceSeeder::class,
                //PbxHistoryCallIndiSeeder::class,
                PbxCdrTaxSeeder::class,
                //AvalaraExem
                DemoSeeder::class,
                //
                //BandwidthSeeder::class,
                //PbxTenantCdrSeeder::class,
                ExtStatusSendEmailSeeder::class,
            ]);

        }

    }
}
