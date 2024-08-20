<?php

use Crater\Mail\SendInvoiceMail;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\InvoiceLateFee;
use Crater\Models\Tax;
use Crater\Models\User;

beforeEach(function () {
    Storage::fake();
    Mail::fake();
    Http::fake();
});

test('fixed fee Works', function () {

    $user = setSettings();
    GenerateInvoice($user);

    $this->artisan('invoice:generateLateFee');

    $tax = Tax::first();
    $invoice = Invoice::first();
    $taxes = (int)($invoice->late_fee_amount * $tax->percent);
    $total = 127 + $taxes;

    Log::debug($total);
    expect($invoice)->late_fee_amount->toBe(7)
        ->sub_total->toBe(100)
        ->due_amount->toBe($total)
        ->late_fee_taxes->toBe($taxes)
        ->and(InvoiceLateFee::first())->subtotal->toBe($invoice->late_fee_amount)
        ->tax_amount->toBe($taxes)
        ->total->toBe($invoice->late_fee_amount + $taxes)
        ->notice->toBe('invoice_late_fee_one');
    Mail::assertSent(SendInvoiceMail::class, 1);
});

test('percentage fee Works', function () {

    $user = setSettings();
    GenerateInvoice($user, 7);

    $this->artisan('invoice:generateLateFee');

    $tax = Tax::first();
    $invoice = Invoice::first();
    $taxes = (int)($invoice->late_fee_amount * $tax->percent);
    $feeAmount = (int)(100 * 0.25);
    $total = 120 + $feeAmount + $taxes;

    Log::debug($total);
    expect($invoice)->late_fee_amount->toBe($feeAmount)
        ->sub_total->toBe(100)
        ->due_amount->toBe($total)
        ->late_fee_taxes->toBe($taxes)
        ->and(InvoiceLateFee::first())->subtotal->toBe($invoice->late_fee_amount)
        ->tax_amount->toBe($taxes)
        ->total->toBe($invoice->late_fee_amount + $taxes)
        ->notice->toBe('invoice_late_fee_two');
    Mail::assertSent(SendInvoiceMail::class, 1);

});

function GenerateInvoice(User $user, int $lateBy = 5, array $overWrite = [])
{
    $values = [
        'company_id' => $user->company_id,
        'due_date' => now()->subDays($lateBy),
        'sub_total' => 100,
        'due_amount' => 120,
        'status' => Invoice::STATUS_VIEWED,
        'paid_status' => Invoice::STATUS_UNPAID,
    ];

    $values = array_merge($values, $overWrite);

    $invoice = Invoice::factory()->lateFee()->create($values);

    Tax::factory()->create([
        'company_id' => $user->company_id,
        'invoice_id' => $invoice->id,
    ]);
}

function setSettings(): User
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

    CompanySetting::insert([
        [
            'company_id' => $user->company_id,
            'option' => 'late_fee_hour',
            'value' => now()->format('H:i'),
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_active_one',
            'value' => '1',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_active_two',
            'value' => '1',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_days_one',
            'value' => '5',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_days_two',
            'value' => '7',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_type_one',
            'value' => 'fixed',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_type_two',
            'value' => 'percentage',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_type_one_value',
            'value' => '0.07',
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_late_fee_type_two_value',
            'value' => '0.25',
        ],
    ]);

    return $user;
}
