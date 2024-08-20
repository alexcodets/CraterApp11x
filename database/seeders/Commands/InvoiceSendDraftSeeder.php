<?php

namespace Database\Seeders\Commands;

use Crater\Models\Company;
use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class InvoiceSendDraftSeeder extends Seeder
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
                'option' => 'allow_send_invoice_job',
                'value' => 1,
            ],
            [
                'option' => 'send_email_deactive',
                'value' => 'NO',
            ],
            [
                'option' => 'period_time_run_send_invoice_job',
                'value' => 15,
            ],
            /*[
                            'option' => 'time_invoices_draft_sent',
                            'value' => now()->format('Y-m-d H:i'),
           ],*/
        ]);

        Invoice::factory([
            'user_id' => $user->id,
            'company_id' => $company->id,
            'status' => Invoice::STATUS_DRAFT
        ])->create();
    }
}
