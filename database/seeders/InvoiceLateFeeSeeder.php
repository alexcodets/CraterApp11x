<?php

namespace Database\Seeders;

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\User;
use Illuminate\Database\Seeder;

class InvoiceLateFeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $user = User::factory()->for(Company::factory(), 'company')->create([
            'balance' => 30,
            'auto_debit' => '1',
            'status_payment' => 'prepaid',
            'minimun_balance' => 10,
            'auto_replenish_amount' => 13,
            'role' => 'customer',
            'status_customer' => 'A',
        ]);

        /* @var User $user */
        $id = $user->company_id;

        $invoice = Invoice::factory()->lateFee()->create([
            'company_id' => $id,
            'due_date' => now()->addDays(5),
            'sub_total' => 100,
            'due_amount' => 120,
        ]);
        $invoice = Invoice::factory()->lateFee()->create(['company_id' => $id, 'due_date' => now()->addDays(5)]);
        $invoice = Invoice::factory()->lateFee()->create(['company_id' => $id, 'due_date' => now()->addDays(2)]);
        $invoice = Invoice::factory()->lateFee()->create(['company_id' => $id, 'due_date' => now()->addDays(8)]);

        CompanySetting::insert([
            [
                'company_id' => $id,
                'option' => 'late_fee_hour',
                'value' => '12:21'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_active_one',
                'value' => '1'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_active_two',
                'value' => '1'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_days_one',
                'value' => '5'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_days_two',
                'value' => '2'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_type_one',
                'value' => 'fixed'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_type_two',
                'value' => 'percentage'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_type_one_value',
                'value' => '7'
            ],
            [
                'company_id' => $id,
                'option' => 'invoice_late_fee_type_two_value',
                'value' => '0.25'
            ],
        ]);
    }
}
