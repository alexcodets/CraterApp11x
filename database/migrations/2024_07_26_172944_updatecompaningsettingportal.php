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

        // Recorrer todos los registros de la tabla company
        Company::all()->each(function ($company) {
            // Verificar y agregar "enable_make_customer"
            $makeCustomerSetting = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_invoice_customer',
            ]);
            if (! $makeCustomerSetting->exists) {
                $makeCustomerSetting->value = 0;
                $makeCustomerSetting->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $creditCustomerSetting = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_quotes_customer',
            ]);
            if (! $creditCustomerSetting->exists) {
                $creditCustomerSetting->value = 0;
                $creditCustomerSetting->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $enable_payment_customer = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_payment_customer',
            ]);
            if (! $enable_payment_customer->exists) {
                $enable_payment_customer->value = 0;
                $enable_payment_customer->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $enable_report_customer = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_report_customer',
            ]);
            if (! $enable_report_customer->exists) {
                $enable_report_customer->value = 0;
                $enable_report_customer->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $enable_service_customer = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_service_customer',
            ]);
            if (! $enable_service_customer->exists) {
                $enable_service_customer->value = 0;
                $enable_service_customer->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $enable_tickets_customer = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_tickets_customer',
            ]);
            if (! $enable_tickets_customer->exists) {
                $enable_tickets_customer->value = 0;
                $enable_tickets_customer->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $enable_paymentaccount_customer = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_paymentaccount_customer',
            ]);
            if (! $enable_paymentaccount_customer->exists) {
                $enable_paymentaccount_customer->value = 0;
                $enable_paymentaccount_customer->save();
            }

            // Verificar y agregar "enable_credit_customer"
            $enable_pbxservice_customer = CompanySetting::firstOrNew([
                'company_id' => $company->id,
                'option' => 'enable_pbxservice_customer',
            ]);
            if (! $enable_pbxservice_customer->exists) {
                $enable_pbxservice_customer->value = 0;
                $enable_pbxservice_customer->save();
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
