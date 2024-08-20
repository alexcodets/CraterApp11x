<?php

namespace Database\Seeders\Commands;

use Crater\Models\Company;
use Crater\Models\Contacts;
use Crater\Models\PaymentAccount;
use Crater\Models\User;
use Crypt;
use Illuminate\Database\Seeder;

class CreditCardReminderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::factory()->for(Company::factory(), 'company')->create();

        /* @var User $user */
        $company = $user->company;

        $company->settings()->createMany([
            [
                'option' => 'allow_cardexpiration_payment_job',
                'value' => 1,
            ],
            [
                'option' => 'time_run_cardexpiration_payment_job',
                'value' => now()->format('H:i'),
            ],
            [
                'option' => 'period_time_run_send_invoice_job',
                'value' => 15,
            ],
            [
                'option' => 'payment_bbc_email',
                'value' => 'bbc@corebilcrater.com',
            ],
            [
                'option' => 'payment_card_expiration_reminders_subject',
                'value' => '<h3 class="title">Return Values<a class="genanchor" href="#refsect1-function.strip-tags-returnvalues"> asd</a></h3>',
            ]
        ]);

        PaymentAccount::factory()->create([
            'payment_account_type' => 'CC',
            'company_id' => $company->id,
            'client_id' => $user->id,
            'expiration_date' => Crypt::encryptString(now()->addMonth()->format('Y-m')),
        ]);

        User::factory([
            'role' => 'super admin',
            'email_payments' => 1,
        ])->count(2)->create();

        Contacts::factory([
            'customer_id' => $user->id,
        ])->count(3)->create();

        //TODO: x Agregar usuario admin, con super admin con atributo email_payments
        // x Crear CompanyConfig payment_card_expiration_reminders_subject con valor = '<h3 class="title">Return Values<a class="genanchor" href="#refsect1-function.strip-tags-returnvalues"> asd</a></h3>'
        // x Crear contact Factory
        // x Crear contactos del usuario principal con atributos allow_receive_emails== 1, email_payments== 1.
        // x Crear companySetting  ->where('option', 'payment_bbc_email') value = bbc@corebilcrater.com
        // x Test subject.
        // Test TSOOI
    }
}
