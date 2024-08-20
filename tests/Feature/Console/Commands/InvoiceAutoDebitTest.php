<?php

use Crater\Authorize\Models\Authorize;
use Crater\Authorize\Models\AuthorizeSetting;
use Crater\Mail\EmailLowNotification;
use Crater\Mail\FailedPaymentMail;
use Crater\Mail\PaymentAccepted;
use Crater\Models\BalanceCustomer;
use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Crater\Models\CustomerConfig;
use Crater\Models\FailedPaymentHistory;
use Crater\Models\Invoice;
use Crater\Models\Payment;
use Crater\Models\PaymentAccount;
use Crater\Models\PaymentGateways;
use Crater\Models\PaymentMethod;
use Crater\Models\PaypalSetting;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Database\Seeders\CurrenciesTableSeeder;
use net\authorize\api\contract\v1\ANetApiResponseType;
use net\authorize\api\contract\v1\MessagesType;
use net\authorize\api\contract\v1\MessagesType\MessageAType;

beforeEach(function () {
    $this->seed([
        CurrenciesTableSeeder::class
    ]);

    Storage::fake();
    Mail::fake();
    Http::fake();
});

it('does not work with Invoices in the wrong min', function () {
    $user = postPago();
    configuration($user);
    makeInvoices($user);

    CompanySetting::where('option', '=', 'time_run_autodebit_customer_job')
        ->where('company_id', '=', $user->company_id)
        ->update(['value' => now()->addMinutes(3)->format('H:i')]);

    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(32.00)
        ->and(Invoice::sum('due_amount'))->toBe('3000')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_UNPAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(0)
        ->and(Payment::count())->toBe(0);
    Mail::assertNothingSent();
})->skip();

it('does not work with Invoices in the wrong day', function () {
    $user = postPago();
    configuration($user);
    makeInvoices($user);

    Invoice::whereNotNull('id')->update(['due_date' => now()->addDays(4)]);

    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(32.00)
        ->and(Invoice::sum('due_amount'))->toBe('3000')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_UNPAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(0)
        ->and(Payment::count())->toBe(0);
    Mail::assertNothingSent();
});

test('postPago User with balance 0 will not try to pay', function () {
    $user = postPago();
    $user->balance = 0;
    $user->save();
    configuration($user);
    makeInvoices($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('3000')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_UNPAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(0)
        ->and(Payment::count())->toBe(0);

    Mail::assertSent(EmailLowNotification::class, 1);

});

test('postPago User with enough balance can pay all the invoices', function () {
    $user = postPago();
    configuration($user);
    makeInvoices($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(2.00)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(3)
        ->and(Payment::count())->toBe(3);

    Mail::assertSent(EmailLowNotification::class, 1);

});

test('postPago User with enough balance at the end will not be sent a email of low balance', function () {
    $user = postPago();
    $user->balance = 80;
    $user->save();
    configuration($user);
    makeInvoices($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(50.00)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(3)
        ->and(Payment::count())->toBe(3);

    Mail::assertNothingSent();

});

test('postPago user can make a single partial payment', function () {
    $user = postPago();
    configuration($user);
    $user->balance = 7;
    $user->save();
    makeInvoices($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.00)
        ->and(Invoice::sum('due_amount'))->toBe('2300')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PARTIALLY_PAID)->count())->toBe(1)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_UNPAID)->count())->toBe(2)
        ->and(BalanceCustomer::count())->toBe(1)
        ->and(Payment::count())->toBe(1);

    //Mail::assertSent(EmailLowNotification::class, 1);

});

test('postPago user can make total and partial payment', function () {
    $user = postPago();
    configuration($user);
    $user->balance = 28;
    $user->save();
    makeInvoices($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.00)
        ->and(Invoice::sum('due_amount'))->toBe('200')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PARTIALLY_PAID)->count())->toBe(1)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(2)
        ->and(BalanceCustomer::count())->toBe(3)
        ->and(Payment::count())->toBe(3);

    Mail::assertSent(EmailLowNotification::class, 1);

});
// Prepago;
test('PrePago user Has no payment Account Type', function () {
    $user = prePago();
    $user->balance = 12;
    $user->save();
    //Log::debug($user);
    configuration($user);
    makeInvoices($user);
    PaymentAccount::where('id', '>', 0)->update([
        'payment_account_type' => null
    ]);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('1800')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(1)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PARTIALLY_PAID)->count())->toBe(1)
        ->and(BalanceCustomer::count())->toBe(2)
        ->and(Payment::count())->toBe(2)
        ->and(FailedPaymentHistory::count())->toBe(0);

    Mail::assertNothingSent();

});

test('PrePago CC user has no default gateway', function () {
    $user = prePago();
    $user->balance = 12;
    $user->save();
    //Log::debug($user);
    configuration($user);
    makeInvoices($user);
    PaymentGateways::where('id', '>', 0)->update([
        'default' => 0
    ]);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('1800')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(1)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PARTIALLY_PAID)->count())->toBe(1)
        ->and(BalanceCustomer::count())->toBe(2)
        ->and(Payment::count())->toBe(2)
        ->and(FailedPaymentHistory::count())->toBe(0);

    Mail::assertNothingSent();

});

test('PrePago Paypal user wont try to recharge credit if has enough balance to pay all', function () {
    $user = prePago();
    $user->balance = 75;
    $user->save();
    configuration($user);
    makeInvoices($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(45.00)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(3)
        ->and(Payment::count())->toBe(3);

    Mail::assertNothingSent();

})->skip();

test('PrePago CC Paypal user with Wrong credentials can pay all the invoices with enough balance', function () {
    $user = prePago();
    configuration($user);
    makeInvoices($user);
    ccConfiguration($user);

    $this->artisan('Invoice:Autodebit');

    Log::debug(FailedPaymentHistory::first());

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(3)
        ->and(Payment::count())->toBe(3)
        ->and(FailedPaymentHistory::count())->toBe(3)
    ->and(FailedPaymentHistory::first())->description->toBe(__('payment.authorize.error.authentication'));


    Mail::assertSent(FailedPaymentMail::class, 3);

})->skip();

test('PrePago CC Paypal user with Wrong credentials can pay after trying and fail to recharge', function () {
    $user = prePago();
    $user->balance = 12;
    $user->save();
    //Log::debug($user);
    configuration($user);
    makeInvoices($user);
    ccConfiguration($user);

    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('1800')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(1)
        ->and(BalanceCustomer::count())->toBe(2)
        ->and(Payment::count())->toBe(2)
        ->and(FailedPaymentHistory::count())->toBe(3);
    //FailedPaymentHistory

    Mail::assertSent(FailedPaymentMail::class, 3);

})->skip();

test('PrePago CC Authorize user wrong credentials payment', function () {
    $user = prePago();
    $user->balance = 12;
    $user->save();
    //Log::debug($user);
    configuration($user);
    makeInvoices($user);
    ccConfiguration($user);
    AuthorizeSetting::factory()->create([
        'creator_id' => $user->id,
        'company_id' => $user->company->id,
    ]);
    PaymentGateways::where('id', '>', 0)->update([
        'name' => 'Authorize'
    ]);

    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(0.0)
        ->and(Invoice::sum('due_amount'))->toBe('1800')
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(1)
        ->and(BalanceCustomer::count())->toBe(2)
        ->and(Payment::count())->toBe(2)
        ->and(FailedPaymentHistory::count())->toBe(3)
        ->and(FailedPaymentHistory::first())->error_description->toBe('User authentication failed due to invalid authentication values.');

    Mail::assertSent(FailedPaymentMail::class, 3);

});

test('PrePago CC Authorize user payment working', function () {
    $user = prePago();
    $user->balance = 12;
    $user->save();
    //Log::debug($user);
    configuration($user);
    makeInvoices($user);
    authorizeCredentials($user);
    ccConfiguration($user);
    $this->artisan('Invoice:Autodebit');

    expect(User::first())->balance->toBe(13.0)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(3)
        ->and(BalanceCustomer::count())->toBe(3)
        ->and(Payment::count())->toBe(5)
        //->and(Payment::where('notes', 'Auto debit Invoice Payment')->count())->toBe(4)
        ->and(FailedPaymentHistory::count())->toBe(0);

    Mail::assertSent(PaymentAccepted::class, 3);

});

test('PrePago ACH Authorize user payment working', function () {
    $user = prePago();
    $user->balance = 9;
    $user->save();
    //Log::debug($user);
    configuration($user);
    makeInvoices($user);
    authorizeCredentials($user);
    achConfiguration($user);

    $this->artisan('Invoice:Autodebit');

    //Log::debug('Payment');
    //Log::debug(Payment::first());
    //Log::debug('Authorize');
    //Log::debug(Authorize::first());

    // 22 - 10 - 10 - 2

    // 9 + 13 = 22 -10 -10 -2 +8 -8 +13
    // 7: 3A 4D
    /*
    * Testing that a normal payment have certain field and recharge payment have another values for those fields.
    */
    //Log::debug(Payment::skip(1)->take(1)->first());
    expect(User::first())->balance->toBe(13.0)
        ->and(Invoice::sum('due_amount'))->toBe(0)
        ->and(Invoice::where('paid_status', '=', Invoice::STATUS_PAID)->count())->toBe(3)
        ->and(BalanceCustomer::where('status', 'D')->count())->toBe(3)
        ->and(BalanceCustomer::where('status', 'A')->count())->toBe(2)
        ->and(FailedPaymentHistory::count())->toBe(0)
        ->and(Payment::first())->user_id->toBe($user->id)
        ->payment_date->toBe(now()->format('Y-m-d'))
        ->amount->toBe(1300)
        ->notes->toBe('Charge to restore credit')
        ->invoice_id->toBeNull()
        ->authorize_id->not()->toBeNull()
        ->payment_method_id->toBe(PaymentMethod::first()->id)
        ->notes->toBe('Charge to restore credit')
        ->and(Payment::skip(1)->take(1)->first())->user_id->toBe($user->id)
        ->payment_date->toBe(now()->format('Y-m-d'))
        ->payment_method_id->toBeNull()
        ->amount->toBe(1000)
        ->invoice_id->toBe(Invoice::first()->id)
        ->notes->toBe('Auto debit Invoice Payment with Balance')
        ->and(Authorize::first())->transaction_id->not()->toBeNull()
        ->amount->toBe(1300.0)
        ->and(Authorize::count())->toBe(3);

    Mail::assertSent(PaymentAccepted::class, 3);

});

test('PrePago ACH Authorize Felipe Test', function () {
    $user = prePago();
    $user->balance = 300;
    $user->auto_replenish_amount = 120;
    $user->minimun_balance = 110;
    $user->save();
    configuration($user);
    makeCustomInvoices($user, 11, 3_500);
    authorizeCredentials($user);
    achConfiguration($user);

    //$message = getAuthenticaSuccessResponse();

    /*    $mock = $this->mock(AuthorizePaymentService::class, function (MockInterface $mock) use ($message) {
            $mock->shouldReceive('executeWithApiResponse')->once()->andReturn(
                $message
            );
        });*/

    /*$mock = $this->mock(\Crater\Services\Payment\Authorize\PaymentAuthorizeDO::class, function (MockInterface $mock) use ($message) {
        $mock->shouldReceive('getName')->once()->andReturn('Reny Ramos');
    });*/
    //8 + 1 + 3
    // 8 * 35 + 1 * 20 + 1 * 15 + 2 * 35 + 1 * 110
    // 8 * 35 + 1 * 20 + 1 * 15 + 1 * 15 + 2 * 35 + 2 * 35 + 1 * 110
    //8 + 1 + 1 +2 +1 = 13 Payment
    // 8 + 1 +1 +1 +2 +2 +1

    // 12 payment 8(35) + 1(20) + 1(15) + 2(35)
    // 4 recharge: 1(15) + 2(35) + 1(110)

    // 9 + 1 = 10 balance

    $this->artisan('Invoice:Autodebit');

    Log::debug('Payment');
    Log::debug(Payment::count());
    $partialPaymentId = Payment::skip(8)->first()->id;

    Log::debug(Payment::where('id', '>', $partialPaymentId + 1)->first());

    Log::debug($partialPaymentId);
    expect(User::first())->balance->toBe(120.0)
        // Primeros 8 payment por monto 3500 para pago de factura.
        ->and(Payment::where('id', '<=', $partialPaymentId)->where('amount', 3_500)
            ->whereNull('authorize_id')->where('notes', 'Auto debit Invoice Payment')
            ->whereNull('payment_method_id')->count())->toBe(8)
        // Pago 9, seria pago parcial por 2000
        ->and(Payment::find($partialPaymentId))->amount->toBe(2_000)->invoice_id->not()->toBeNull()
        ->and(Payment::where('id', '=', $partialPaymentId + 1)->take(1)->first())
        ->amount->toBe(1_500)
        ->invoice_id->not()->toBeNull()
        // 2 pagos mas de tarjeta saliente
        ->and(Payment::where('id', '>', $partialPaymentId + 1)
            ->where('amount', 3_500)->count())
        ->toBe(2)
        ->and(Payment::count())->toBe(13)
        ->and(BalanceCustomer::where('status', 'D')->count())->toBe(9)
        ->and(BalanceCustomer::where('status', 'A')->count())->toBe(1)
        ->and(BalanceCustomer::whereNotNull('payment_id')->count())->toBe(10)
        ->and(FailedPaymentHistory::count())->toBe(0)
        ->and(Authorize::first())->transaction_id->not()->toBeNull()
        ->amount->toBe(1_500.0)
        ->and(Authorize::where('amount', '=', 3_500.0)->count())->toBe(2)
        ->and(Authorize::latest()->first())->amount->toBe(12_000.00)
        ->and(Authorize::count())->toBe(4)
        ->and(BalanceCustomer::first())->present_balance->toBe(300.0)
        ->amount_op->toBe(35.0)
        ->amount_final->toBe(300.0 - 35.0)
        ->and(BalanceCustomer::where('status', 'A')->first())->present_balance->toBe(0.0)
        ->amount_op->toBe(120.0)
        ->amount_final->toBe(120.0);

    Mail::assertSent(PaymentAccepted::class, 4);


});

function getAuthenticaSuccessResponse(): ANetApiResponseType
{
    $message = new MessageAType();
    $message->setCode('I00001')->setText('Successful.');

    $messageType = new MessagesType();
    $messageType->setResultCode('Ok')->setMessage([$message]);

    $response = new ANetApiResponseType();
    $response->setRefId('452348896')->setMessages($messageType);

    return $response;

}

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
        "company_id" => $user->company_id,
        "default" => 1,
        "status" => "A",
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
    //--
    CompanySetting::create([
        'option' => 'payment_approved_ach',
        'value' => 'Aprobado mensaje ach',
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'payment_approved_ach_subject',
        'value' => 'Ach, Aprobado',
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'payment_approved_credit_card',
        'value' => 'Pago con credit card aprobada',
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'payment_approved_credit_card_subject',
        'value' => 'CC Aprobada',
        'company_id' => $user->company_id,
    ]);

    CompanySetting::create([
        'option' => 'server_notification',
        'value' => $user->email,
        'company_id' => $user->company_id,
    ]);
    //--
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
        'bank_name' => Crypt::encryptString('Pedro Perez'),
    ]);

    AuthorizeSetting::factory()->create([
        'creator_id' => $user->id,
        'company_id' => $user->company->id,
    ]);

    PaymentGateways::where('id', '>', 0)->update([
        'name' => 'Authorize'
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
    //$user = User::first();
    $pbxService = PbxServices::factory()->create([
        'customer_id' => $user->id,
        'company_id' => $user->company_id
    ]);
    /* @var PbxServices $pbxService */

    Invoice::factory()->sent()->times(3)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'pbx_service_id' => $pbxService->id,
        'status' => Invoice::STATUS_DUE,
        'paid_status' => Invoice::STATUS_UNPAID,
        'total' => 500,
        'due_amount' => 1000,
        'due_date' => now()->addDays(3),
    ]);

}

function makeCustomInvoices(User $user, int $quantityInvoice, int $totalEachInvoice)
{
    //$user = User::first();
    $pbxService = PbxServices::factory()->create([
        'customer_id' => $user->id,
        'company_id' => $user->company_id
    ]);
    /* @var PbxServices $pbxService */

    Invoice::factory()->sent()->times($quantityInvoice)->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'pbx_service_id' => $pbxService->id,
        'status' => Invoice::STATUS_DUE,
        'paid_status' => Invoice::STATUS_UNPAID,
        'total' => $totalEachInvoice,
        'due_amount' => $totalEachInvoice,
        'due_date' => now()->addDays(3),
    ]);

}
