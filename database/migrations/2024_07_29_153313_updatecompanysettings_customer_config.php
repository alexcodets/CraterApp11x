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
        //

        Company::all()->each(function ($company) {

            $makeCustomerSetting = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'customer_type_selected',
            ]);
            if (! $makeCustomerSetting->exists) {
                $makeCustomerSetting->value = 'B';
                $makeCustomerSetting->save();
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
