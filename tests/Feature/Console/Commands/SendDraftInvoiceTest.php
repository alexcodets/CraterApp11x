<?php

use Crater\Mail\SendInvoiceMail;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Invoice;
use Crater\Models\User;
use Database\Seeders\Commands\InvoiceSendDraftSeeder;

beforeEach(function () {
    $this->seed([
        InvoiceSendDraftSeeder::class
    ]);

    Storage::fake();
    Mail::fake();
});
it('wont send invoice if the allow send invoice is null or false', function () {
    CompanySetting::where('option', '=', 'allow_send_invoice_job')->update(['value' => 0]);
    $this->artisan('invoice:send:draft');
    Mail::assertNothingSent(SendInvoiceMail::class);
    expect(Invoice::first())->status->toBe(Invoice::STATUS_SENT);
});
it('wont send invoice if send_email_deactive is not equal to NO', function () {
    CompanySetting::where('option', '=', 'send_email_deactive')->update(['value' => true]);
    $this->artisan('invoice:send:draft');
    Mail::assertNothingSent(SendInvoiceMail::class);
    expect(Invoice::first())->status->toBe(Invoice::STATUS_SENT);
});
it('wont send invoice if the last invoice was send too soon', function () {
    CompanySetting::create(
        [
            'option' => 'time_invoices_draft_sent',
            'value' => now()->format('Y-m-d H:i'),
            'company_id' => Company::first()->id
        ]
    );
    $this->artisan('invoice:send:draft');
    Mail::assertNothingSent(SendInvoiceMail::class);
    expect(Invoice::first())->status->toBe(Invoice::STATUS_SENT);

});

it('send invoice if the last invoice was send a time ago', function () {
    CompanySetting::create(
        [
            'option' => 'time_invoices_draft_sent',
            'value' => now()->subMinutes(16)->format('Y-m-d H:i'),
            'company_id' => Company::first()->id
        ]
    );
    $this->artisan('invoice:send:draft');
    Mail::assertSent(SendInvoiceMail::class, 1);
    expect(Invoice::first())->status->toBe(Invoice::STATUS_SENT);
});

it('send one email for each invoice', function () {
    Invoice::factory([
        'user_id' => User::first()->id,
        'company_id' => Company::first()->id,
        'status' => Invoice::STATUS_DRAFT
    ])->count(5)->create();
    $this->artisan('invoice:send:draft');
    Mail::assertSent(SendInvoiceMail::class, 6);
});

test('it works', function () {
    $this->artisan('invoice:send:draft');

    Mail::assertSent(SendInvoiceMail::class, 1);

    expect(Invoice::first())->status->toBe(Invoice::STATUS_SENT);
});
