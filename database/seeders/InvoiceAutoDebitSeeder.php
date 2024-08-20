<?php

namespace Database\Seeders;

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class InvoiceAutoDebitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //Old
        //$user = User::find(2);

        //Cambiar los datos del user para hacer las pruebas.

        $user = User::factory()->for(Company::factory(), 'company')->create();
        /* @var User $user */

        CustomerConfig::factory([
            'company_id' => $user->company_id,
            'creator_id' => $user->id,
            'enable_auto_debit' => true,
            'customer_id' => $user->id,
        ])->create();

        Invoice::factory([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'pbx_service_id' => 1,
            'total' => 100,
            'due_amount' => 100,
        ])->overdue()->unpaid()->create();

        Invoice::factory([
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'pbx_service_id' => 1,
            'total' => 100,
            'due_amount' => 100,
        ])->overdue()->unpaid()->create();

        Invoice::factory([
            'user_id' => 2,
            'company_id' => 1,
            'pbx_service_id' => 1,
            'total' => 100,
            'due_amount' => 100,
        ])->overdue()->unpaid()->create();

        //Low email

        CompanySetting::create([
            'option' => 'customer_email_notification',
            'value' => 'Buen dia {CONTACT_DISPLAY_NAME}, se le esta enviando un mensaje automatizado al email: {CONTACT_EMAIL_LOW_BALANCE_NOTIFICATION} el cual esta configurado como email de contacto, para avisarle que su balance actual: {CONTACT_BALANCE} es menor al balance configurado por usted como balance minimo: {CONTACT_AUTO_REPLENISH_AMOUNT}',
            'company_id' => 1
        ]);

    }
}
