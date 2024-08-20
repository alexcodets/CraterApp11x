<?php

use Crater\Mail\PaymentReminder;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Contacts;
use Database\Seeders\Commands\CreditCardReminderSeeder;

beforeEach(function () {
    $this->seed([
        CreditCardReminderSeeder::class
    ]);

    Storage::fake();
    Mail::fake();
});

test('it works', function () {
    $this->travel(15 - now()->day)->days();
    //Log::debug(Company::first()->settings);
    $this->artisan('payments:payment_accounts:reminder');
    //->expectOutput(__('comandos.creditCardReminder.info.run', ['company' => Company::first()->id, 'account' => PaymentAccount::first()->id]));
    Mail::assertSent(PaymentReminder::class, 7);
});

it('Only works in the 15', function () {
    //Log::debug(Company::first()->settings);
    $this->artisan('payments:payment_accounts:reminder')->expectsOutput(__('comandos.creditCardReminder.info.not_15'));
    Mail::assertNothingSent(PaymentReminder::class);
});

it('will only send mail to customer if they have allow and email_payment active', function () {
    $this->travel(15 - now()->day)->days();
    Contacts::where('id', '>', 0)->update(['allow_receive_emails' => 0, 'email_payments' => 0]);
    $this->artisan('payments:payment_accounts:reminder');
    Mail::assertSent(PaymentReminder::class, 4);

    Contacts::where('id', '>', 0)->update(['allow_receive_emails' => 1, 'email_payments' => 0]);
    $this->artisan('payments:payment_accounts:reminder');
    Mail::assertSent(PaymentReminder::class, 8);

    Contacts::where('id', '>', 0)->update(['allow_receive_emails' => 0, 'email_payments' => 1]);
    $this->artisan('payments:payment_accounts:reminder');
    Mail::assertSent(PaymentReminder::class, 12);

    Contacts::where('id', '>', 0)->update(['allow_receive_emails' => 1, 'email_payments' => 1]);
    $this->artisan('payments:payment_accounts:reminder');
    Mail::assertSent(PaymentReminder::class, 19);

});

it('use config subject when set, even if is null', function () {

    $values = Company::first()->credit_card_reminder_email_setting;
    expect($values['subject'])->toBe('Return Values asd');

    CompanySetting::where('option', '=', 'payment_card_expiration_reminders_subject')->update(['value' => null]);

    $values = Company::first()->credit_card_reminder_email_setting;
    expect($values['subject'])->toBe('');
    CompanySetting::where('option', '=', 'payment_card_expiration_reminders_subject')->delete();

    $values = Company::first()->credit_card_reminder_email_setting;
    expect($values['subject'])->toBe('Payment Credit Card Reminder');

});
