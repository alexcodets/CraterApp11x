<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Recorrer todos los registros de la tabla company
        Company::all()->each(function ($company) {
            // Verificar y agregar "enable_make_customer"
            $makeCustomerSetting = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_make_customer',
            ]);
            if (! $makeCustomerSetting->exists) {
                $makeCustomerSetting->value = 0;
                $makeCustomerSetting->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $creditCustomerSetting = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_credit_customer',
            ]);
            if (! $creditCustomerSetting->exists) {
                $creditCustomerSetting->value = 0;
                $creditCustomerSetting->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
