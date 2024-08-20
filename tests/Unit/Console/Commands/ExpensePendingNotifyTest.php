<?php


use Crater\Mail\ExpensePendingMail;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\Expense;
use Crater\Models\User;

it('wont check expense that are not pending', function () {

    Mail::fake();
    $user = settingBase();
    Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->addDay()->format('Y-m-d'),
        'notification' => true,
    ]);
    $this->artisan('expense:pending-notify');
    Mail::assertNothingSent();

});

it('wont check expense outside the time', function () {

    Mail::fake();
    $user = settingBase();
    CompanySetting::where('option', '=', 'job_expense_pending_time_run')
        ->update(['value' => now()->addMinutes(3)->format('H:i')]);

    Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->format('Y-m-d'),
        'notification' => true,

    ]);
    $this->artisan('expense:pending-notify');
    Mail::assertNothingSent();

});

it('wont check expense with notification disabled', function () {

    Mail::fake();
    $user = settingBase();
    CompanySetting::where('option', '=', 'job_expense_pending_time_run')
        ->update(['value' => now()->addMinutes(3)->format('H:i')]);

    Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->format('Y-m-d'),
        'notification' => false,

    ]);
    $this->artisan('expense:pending-notify');
    Mail::assertNothingSent();

});

it('send single mail', function () {

    //Mail::fake();
    $user = settingBase();
    Expense::factory()->times(1)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->format('Y-m-d'),
        'notification' => true,
    ]);
    Expense::factory()->times(9)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->addDays(10)->format('Y-m-d'),
        'notification' => true,

    ]);
    $this->artisan('expense:pending-notify');
    //Mail::assertSent(ExpensePendingMail::class, 1);

});

it('send only bbc mail', function () {

    Mail::fake();
    $user = settingBase();
    Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->format('Y-m-d'),
        'notification' => true,

    ]);
    $this->artisan('expense:pending-notify');
    Mail::assertSent(ExpensePendingMail::class, 10);

});

it('send bbc and user email', function () {

    Mail::fake();
    $user = settingBase();
    User::factory()->times(2)->create([
        'company_id' => $user->company_id,
        'role' => 'super admin',
        'email_expenses' => 1

    ]);
    Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->format('Y-m-d'),
        'notification' => true,

    ]);
    $this->artisan('expense:pending-notify');
    Mail::assertSent(ExpensePendingMail::class, 30);

});

it('wont send email for another company', function () {

    Mail::fake();
    $user = settingBase();
    User::factory()->for(Company::factory(), 'company')->times(2)->create([
        'role' => 'super admin',
        'email_expenses' => 1
    ]);
    Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'expense_date' => now()->format('Y-m-d'),
        'notification' => true,

    ]);
    $this->artisan('expense:pending-notify');
    Mail::assertSent(ExpensePendingMail::class, 10);

});

function settingBase()
{
    $user = User::factory()->for(Company::factory(), 'company')->create();
    /* @var User $user */

    CompanySetting::insert([
        [
            'company_id' => $user->company_id,
            'option' => 'job_expense_pending_enable',
            'value' => '1'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'job_expense_pending_time_run',
            'value' => now()->format('H:i')
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'job_expense_pending_mail_body',
            'value' => <<<'EOF'
                            <h1>Titulo del Body</h1>
                            <p> <strong>The Expense</strong>: {EXPENSE_NUMBER} with payment number: {PAYMENT_NUMBER} <i>that used the method:<i></i> {PAYMENT_METHOD}
                            for the date: {PAYMENT_DATE} with provider name: {PROVIDER_NAME} for the customer number: {CUSTOMER_NUMBER}
                            and name: {CUSTOMER_NAME} that have a amount of {AMOUNT} and category: {CATEGORY} was to be paid for
                            {DUE_DATE}, it was for the item {ITEM}.</p>

                            <p> The Company ubication {COMPANY_COUNTRY}{COMPANY_STATE}{COMPANY_CITY}{COMPANY_ADDRESS_STREET_1}{COMPANY_ADDRESS_STREET_2}{COMPANY_PHONE}
                            {COMPANY_ZIP_CODE}{STATE_CODE} </p>
                            EOF

        ],
        [
            'company_id' => $user->company_id,
            'option' => 'job_expense_pending_mail_subject',
            'value' => 'Pending expense'
        ],
        [
            'company_id' => $user->company_id,
            'option' => 'job_expense_pending_mail_bbc',
            'value' => 'mailito@mailto.com'
        ],
    ]);


    return $user;


    /*Expense::factory()->times(10)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
    ]);*/
}
