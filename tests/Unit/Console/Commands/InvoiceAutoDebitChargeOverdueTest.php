<?php

use Crater\Authorize\Models\AuthorizeSetting;
use Crater\Mail\EmailLowNotification;
use Crater\Models\BalanceCustomer;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\CustomerPackage;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentMethod;
use Crater\Models\PaypalSetting;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Database\Seeders\CurrenciesTableSeeder;

beforeEach(function () {
    $this->seed([
        CurrenciesTableSeeder::class,
    ]);

    Storage::fake();
    Mail::fake();
    Http::fake();
});

test('postPago User with balance 0 will not try to pay', function () {
    $user = postPago();
    $user->balance = 0;
    $user->save();
    configuration($user);
    makeInvoices($user);
    $this->artisan('Invoice:AutoDebitChargeOverdue');

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('30')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_UNPAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(0)
        ->and(Payment::count())->toBe(0);

    Mail::assertSent(EmailLowNotification::class, 1);

});

test('postPago for testing User with enough balance can pay all the invoices in order', function () {
    $user = postPago();
    configuration($user);
    makeShuffleInvoices($user);
    $this->artisan('Invoice:AutoDebitChargeOverdue');


    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('2800')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)
            ->where('status', '=', Invoice::STATUS_COMPLETED)->count())->toBe(3)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PARTIALLY_PAID)
            ->where('status', '=', Invoice::STATUS_OVERDUE)->count())->toBe(1)
        ->and(BalanceCustomer::where('status', 'D')->count())->toBe(4)
        ->and(BalanceCustomer::first())
        ->present_balance->toBe(32.0)
        ->amount_final->toBe(22.0)
        ->amount_op->toBe(10.0)
        ->payment_id->toBe(Payment::first()->id)
        ->and(Payment::count())->toBe(4)
        ->and(Payment::first()->invoice_id)->toBe(Invoice::skip(3)->first()->id)
        ->and(Payment::skip(1)->first())
        ->invoice_id->toBe(Invoice::skip(1)->first()->id)
        ->amount->toBe(1000)
        ->and(Payment::skip(2)->first())
        ->invoice_id->toBe(Invoice::skip(4)->first()->id)
        ->and(Payment::skip(3)->first())
        ->invoice_id->toBe(Invoice::first()->id)
        ->amount->toBe(200)
        ->and(Payment::count())->toBe(4);

    // skip(3) - skip(1) - skip(4) - first() - skip(2) - skip(5)
    //// 4,2,5,1,3,6
    Mail::assertSent(EmailLowNotification::class, 1);

});

test('postPago User with enough balance can pay all the invoices in order', function () {
    $user = postPago();
    $user->balance = 60;
    $user->save();
    configuration($user);
    makeShuffleInvoices($user);
    $this->artisan('Invoice:AutoDebitChargeOverdue');
    $user->refresh();

    expect($user)->balance->toBe(0.00)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(6)
        ->and(BalanceCustomer::count())->toBe(6)
        ->and(Payment::count())->toBe(6)
        ->and(Payment::first()->invoice_id)->toBe(Invoice::skip(3)->first()->id)
        ->and(Payment::skip(1)->first()->invoice_id)->toBe(Invoice::skip(1)->first()->id)
        ->and(Payment::skip(2)->first()->invoice_id)->toBe(Invoice::skip(4)->first()->id)
        ->and(Payment::skip(3)->first()->invoice_id)->toBe(Invoice::first()->id)
        ->and(Payment::skip(4)->first()->invoice_id)->toBe(Invoice::skip(2)->first()->id)
        ->and(Payment::skip(5)->first()->invoice_id)->toBe(Invoice::skip(5)->first()->id)
        ->and(Payment::count())->toBe(6)->and(Payment::count())->toBe(6);

    // skip(3) - skip(1) - skip(4) - first() - skip(2) - skip(5)
    //// 4,2,5,1,3,6
    Mail::assertSent(EmailLowNotification::class, 1);

});

function postPago(): User
{
    /* @var User $user */

    $user = User::factory()->for(Company::factory(), 'company')->create([
        'balance' => 32,
        'auto_replenish_amount' => 8,
        'auto_debit' => '0',
        'email_low_balance_notification' => 13,
        'status_payment' => 'postpaid',
        'role' => 'customer',
        'status_customer' => 'A',
    ]);

    return $user;

}

function prePago(): User
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

    PaypalSetting::factory()->create([
        'creator_id' => $user->id,
    ]);

    PaymentGateways::create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'Paypal',
    ]);

    return $user;
}

function configuration(User $user)
{
    CustomerConfig::factory([
        'company_id' => $user->company_id,
        'creator_id' => $user->id,
        'enable_auto_debit' => 1,
        'customer_id' => $user->id,
        'auto_debit_days_before_due' => 3,
        'auto_apply_credits' => 1,
        'auto_debit_attempts' => 3,
    ])->create();

    CompanySetting::create([
        'option' => 'allow_autodebit_customer_job',
        'value' => 1,
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'time_run_autodebit_customer_job',
        'value' => now()->format('H:i'),
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'customer_email_notification',
        'value' => 'Recharge, you have not enough money for the honey',
        'company_id' => $user->company_id,
    ]);

    PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'company_id' => $user->company_id,
        'status' => 'A',
        'main_account' => 1,
        'payment_account_type' => 'CC',
    ]);
}

function authorizeCredentials($user)
{
    PaymentAccount::where('id', '>', 0)->update([
        'cvv' => Crypt::encryptString(900),
        'credit_card' => 'American Express',
        'card_number' => Crypt::encryptString('370000000000002'),
        'expiration_date' => Crypt::encryptString('2023-12'),
        'payment_account_type' => 'CC',
        //no seguro de ach_type
        'ACH_type' => Crypt::encryptString('checking'),
        'account_number' => Crypt::encryptString('123 456 789 987 898'),
        'routing_number' => Crypt::encryptString('063100277'),
        //'num_check',
        'bank_name' => 'Pedro Perez',
    ]);

    AuthorizeSetting::factory()->create([
        'creator_id' => $user->id,
        'company_id' => $user->company->id,
        'login_id' => '63hHL6wm',
        'transaction_key' => '73nw334PPQ7Sn3vd',
    ]);

    PaymentGateways::where('id', '>', 0)->update([
        'name' => 'Authorize',
    ]);
}

function ccConfiguration(user $user)
{
    PaymentMethod::create([
        'status' => 'A',
        'account_accepted' => 'C',
        'user_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    PaymentAccount::where('id', '>', 0)->update([
        'payment_account_type' => 'CC',
    ]);

}

function achConfiguration(user $user)
{
    PaymentMethod::create([
        'status' => 'A',
        'account_accepted' => 'A',
        'user_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    PaymentAccount::where('id', '>', 0)->update([
        'payment_account_type' => 'ACH',
        'ACH_type' => Crypt::encryptString('checking'),
        'account_number' => Crypt::encryptString('129456558887898'),
        'routing_number' => Crypt::encryptString('026009593'),
        'first_name' => 'prueba2',
    ]);

}

function makeInvoices(User $user)
{
    $pbxService = PbxServices::factory()->create([
        'customer_id' => $user->id,
        'company_id' => $user->company_id,
    ]);
    /* @var PbxServices $pbxService */

    Invoice::factory()->sent()->times(3)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'pbx_service_id' => $pbxService->id,
        'status' => Invoice::STATUS_OVERDUE,
        'paid_status' => Invoice::STATUS_UNPAID,
        'total' => 10,
        'due_amount' => 10,
        'due_date' => now()->subDays(3),
    ]);

}

function makeShuffleInvoices(User $user)
{
    $pbxService = PbxServices::factory()->create([
        'customer_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    $customerPackage = CustomerPackage::factory()->create([
        'customer_id' => $user->id,
        'company_id' => $user->company_id,
    ]);

    /* @var CustomerPackage $customerPackage */
    /* @var PbxServices $pbxService */

    $baseValues = [
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'status' => Invoice::STATUS_OVERDUE,
        'paid_status' => Invoice::STATUS_UNPAID,
        'total' => 3000,
        'due_amount' => 1000,
    ];

    // skip(3) - skip(1) - skip(4) - first() - skip(2) - skip(5)

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'customer_packages_id' => $customerPackage->id,
            'due_date' => now()->subDays(3),
        ])
    );

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'pbx_service_id' => $pbxService->id,
            'due_date' => now()->subDays(3),
        ])
    );

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'due_date' => now()->subDays(5),
        ])
    );

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'pbx_service_id' => $pbxService->id,
            'due_date' => now()->subDays(5),
        ])
    );

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'customer_packages_id' => $customerPackage->id,
            'due_date' => now()->subDays(5),
        ])
    );

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'due_date' => now()->subDays(3),
        ])
    );

    Invoice::factory()->sent()->times(1)->create(
        array_merge($baseValues, [
            'due_date' => now()->subDays(3),
            'deleted_at' => now(),
        ])
    );

}
