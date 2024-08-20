<?php

use Crater\Mail\SendInvoiceMail;
use Crater\Models\Address;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\Invoice;
use Crater\Models\User;

beforeEach(function () {
    Storage::fake();
    Mail::fake();
    Http::fake();
});

test('If no dueDate matches it wont send notification', function () {
    $user = setSettings();
    GenerateInvoice($user, 3);

    $this->artisan('Invoice:notice');

    Mail::assertNothingSent();

});

test('If disabled notice it wont send notification', function () {
    $user = setSettings(0);
    GenerateInvoice($user, -3);

    $this->artisan('Invoice:notice');

    Mail::assertNothingSent();

});

test('If date matches but attempts dont match it wont send notification', function () {
    $user = setSettings();
    GenerateInvoice($user, -3, 1, ['attempts' => 1]);
    GenerateInvoice($user, -3, 1, ['attempts' => 2]);
    GenerateInvoice($user, 2, 1, ['attempts' => 0]);
    GenerateInvoice($user, 2, 1, ['attempts' => 2]);
    GenerateInvoice($user, 5, 1, ['attempts' => 0]);
    GenerateInvoice($user, 5, 1, ['attempts' => 1]);


    $this->artisan('Invoice:notice');

    Mail::AssertNothingSent();

    expect(Invoice::where('autodebit_notices_check', '=', 1)->count())->toBe(0)
        ->and(Invoice::where('autodebit_notices_check', '=', 0)->count())->toBe(6);
});

test('It send Before first notification when date matches', function () {
    $user = setSettings();
    GenerateInvoice($user, 3);
    GenerateInvoice($user, -3);

    $this->artisan('Invoice:notice');

    Mail::assertSent(SendInvoiceMail::class, 1);

    expect(Invoice::first())->autodebit_notices_check->toBe(0)
        ->attempts->toBe(0)
        ->and(Invoice::skip(1)->first())->autodebit_notices_check->toBe(1)
        ->attempts->toBe(1);
});


test('It send After second notification when attempts and date match', function () {
    $user = setSettings();

    GenerateInvoice($user, 2, 1, ['attempts' => 1]);
    $this->artisan('Invoice:notice');
    Mail::assertSent(SendInvoiceMail::class, 1);

    expect(Invoice::first())->autodebit_notices_check->toBe(1)
        ->attempts->toBe(2);
});

test('It send After third notification when attempts and date match', function () {
    $user = setSettings();

    GenerateInvoice($user, 5, 1, ['attempts' => 2]);
    $this->artisan('Invoice:notice');
    Mail::assertSent(SendInvoiceMail::class, 1);

    expect(Invoice::first())->autodebit_notices_check->toBe(1)
        ->attempts->toBe(3);
});


function setSettings($notice = 1): User
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

    Address::factory()->billing()->create([
        'payment_notices' => $notice,
        'user_id' => $user->id,
    ]);
    /* @var User $user */

    CustomerConfig::factory()->create(['customer_id' => $user->id, 'enable_auto_debit' => 1]);

    //enable_auto_debit

    CompanySetting::insert([
        [
            'company_id' => $user->company_id,
            'option' => 'late_fee_hour',
            'value' => '12:21'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notices_settings_notice_1',
            'value' => '3'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notices_settings_notice_2',
            'value' => '2'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notices_settings_notice_3',
            'value' => '5'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notices_settings_notice_1_type',
            'value' => 'Before'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notices_settings_notice_2_type',
            'value' => 'After'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notices_settings_notice_3_type',
            'value' => 'After'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notice_one',
            'value' => 'Este es el primer aviso, esta pendiente'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notice_two',
            'value' => 'Segundo aviso, tienes facturas retrasadas'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notice_three',
            'value' => 'Tercer y ultimo aviso, ya no enviaremos mas avisos'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notice_one_subject',
            'value' => 'Primer Aviso'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notice_two_subject',
            'value' => 'Segundo aviso'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'invoice_notice_three_subject',
            'value' => 'Tercer Aviso'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'allow_reminder_payment_job',
            'value' => 1
        ], [
            'company_id' => $user->company_id,
            'option' => 'time_run_reminder_payment_job',
            'value' => now()->format('H:i'),
        ],
    ]);

    return $user;
}

function GenerateInvoice(User $user, int $dueDays = 5, int $times = 1, array $overWrite = [])
{
    $values = [
        'company_id' => $user->company_id,
        'user_id' => $user->id,
        'due_date' => now()->subDays($dueDays),
        'sub_total' => 100,
        'due_amount' => 120,
        'status' => Invoice::STATUS_OVERDUE,
        'paid_status' => Invoice::STATUS_UNPAID,
    ];


    $values = array_merge($values, $overWrite);

    $invoice = Invoice::factory()->times($times)->create($values);
    Log::debug('Invoice');
    Log::debug($invoice);

}
