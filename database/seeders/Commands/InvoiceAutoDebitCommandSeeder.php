<?php

namespace Database\Seeders\Commands;

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class InvoiceAutoDebitCommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // TODO: user prepago y user Postpago
        $user = User::factory([
            'balance' => 300,
            'auto_debit' => true,
            'status_payment' => 'prepaid',
            'minimun_balance' => 100,
            'auto_replenish_amount' => 125,
        ])->for(Company::factory(), 'company')->create();
        /* @var User $user */

        //prepago (envio mail)
        $userB = User::factory([
            'balance' => 300,
            'auto_replenish_amount' => 50,
            'auto_debit' => false,
            'email_low_balance_notification' => 110,
            'status_payment' => 'postpaid',
        ])->for(Company::factory(), 'company')->create();

        $pbxService = PbxServices::factory()->create([
            'customer_id' => $user->id,
            'company_id' => $user->company_id
        ]);
        /* @var PbxServices $pbxService */

        CustomerConfig::factory([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'enable_auto_debit' => 1,
            'customer_id' => $user->id,
            'auto_debit_days_before_due' => 3,
            'automatic_apply_balance' => 1,
        ])->create();


        Invoice::factory([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'pbx_service_id' => $pbxService->id,
            'total' => 100,
            'due_amount' => 100,
        ])->overdue()->times(3)->create();

        //Low email

        CompanySetting::create([
            'option' => 'customer_email_notification',
            'value' => 'Buen dia {CONTACT_DISPLAY_NAME}, se le esta enviando un mensaje automatizado al email: {CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION} el cual esta configurado como email de contacto, para avisarle que su balance actual: {CONTACT_BALANCE} es menor al balance configurado por usted como balance minimo: {CONTACT_AUTO_REPLENISH_AMOUNT}',
            'company_id' => 1
        ]);
    }
}
