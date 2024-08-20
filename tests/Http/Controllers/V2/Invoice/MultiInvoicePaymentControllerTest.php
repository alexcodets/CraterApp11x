<?php

namespace Tests\Http\Controllers\V2\Invoice;

use Crater\DataObject\PaymentAccountData;
use Crater\Models\AuxVaultSetting;
use Crater\Models\{AuxVault,
    Company,
    Currency,
    Invoice,
    Payment,
    PaymentAccount,
    PaymentGateways,
    PaymentGatewaysFee,
    PaymentMethod,
    TransactionFees,
    User};
use Crater\Traits\FastMigrationTrait;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;
use Log;

use function Pest\Laravel\postJson;

uses(FastMigrationTrait::class);

//uses(RefreshDatabase::class);
function generatePaymentData(User $user)
{

    PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'payment_account_type' => 'CC',
    ]);
    PaymentMethod::factory()->create([
        'status' => 'A',
        'account_accepted' => 'C',
    ]);

    PaymentGateways::factory()->create([
        'company_id' => $user->company_id,
        'default' => 1,
        'status' => 'A',
        'name' => 'AuxVault',
    ]);

    AuxVaultSetting::factory()->create([
        'user_id' => $user->id,
        'company_id' => $user->company_id,
        'endpoint' => config('services.aux_vault.url'),
        'api_key' => config('services.aux_vault.api_key'),
        'merchant_id' => config('services.aux_vault.merchant_id'),
        'default' => true,
    ]);

}

test('auth is required', function () {
    postJson(route('generic.multi-payment'))->assertStatus(401);
});
// Validation
test('invoices and amount are required', function (): void {
    Sanctum::actingAs(User::factory(['role' => 'client'])->create());
    //postJson(route('generic.multi-payment', ['user' => User::factory()->create()]))->assertStatus(403);
    $response = postJson(route('generic.multi-payment'))
        ->assertStatus(422)
        ->assertJson([
            'success' => false,
            'message' => 'Validation errors',
            'data' =>
                [
                    'invoices' => ['The invoices field is required.'],
                    'amount' => ['The amount field is required.',],
                ],
            'code' => 422,
        ]);
});
test('invoices must be valid a array', function (): void {
    Sanctum::actingAs(User::factory(['role' => 'client'])->create());
    $response = postJson(route('generic.multi-payment'), ['invoices' => '1'])
        ->assertStatus(422)
        ->assertJsonFragment([
            'The invoices must be an array.',
        ]);

    Log::debug($response->json());
});
test('invoices must contain only valid invoice id', function (): void {
    Sanctum::actingAs(User::factory(['role' => 'client'])->create());
    $response = postJson(route('generic.multi-payment'), ['invoices' => [15, 20, 30, 42]])
        ->assertStatus(422)
        ->assertJsonFragment([
            'The id 15 is not a valid invoice Id',
        ]);

    Log::debug($response->json());
});
test('invoices array work with correct ids', function (): void {
    Sanctum::actingAs($user = User::factory()->create());

    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 25,
    ]);
    $response = postJson(route('generic.multi-payment'), ['invoices' => [$invoice->id]])
        ->assertStatus(422)
        ->assertJsonMissing([
            "The id {$invoice->id} is not a valid invoice Id.",
        ]);

    Log::debug($response->json());

});

test('The payment fees field is required when payment fee is 1', function (): void {
    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, [
        'invoices' => [$invoice->id, $invoiceAlt->id],
        'amount' => 250,
        'payment_fee' => 1,
    ]);

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertStatus(422)
        ->assertJsonFragment([
            'The payment fees field is required when payment fee is 1.',
        ]);
    Log::debug($response->json());

});
test('The payment fees field must be a valid forma array', function (): void {
    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, [
        'invoices' => [$invoice->id, $invoiceAlt->id],
        'amount' => 250,
        'payment_fee' => 1,
        'payment_fees' => [['name' => 'fee', 'amount' => 100]],
    ]);

    //'payment_fees' => [['name' => 'fee', 'amount' => 100, 'type' => 'percentage']],

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertStatus(422)
        ->assertJsonFragment([
            'The payment_fees.0.type field is required.',
        ]);
    Log::debug($response->json());

});

//Aux vault.
test('amount cannot be higher than the invoices total', function (): void {
    Sanctum::actingAs($user = User::factory()->create());
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 250,
    ]);
    $response = postJson(route('generic.multi-payment'), ['invoices' => [$invoice->id], 'amount' => 300])
        ->assertStatus(422)
        ->assertJsonFragment([
            'The amount to pay cannot be higher than the due amount of all the invoices to pay.',
        ]);
    //'The amount (300) may not be greater than the total of the invoices, 250.'
});
test('amount can be the same as the invoices total', function (): void {
    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, ['invoices' => [$invoice->id, $invoiceAlt->id], 'amount' => 250]);

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertOk();
    Log::debug($response->json());

    //    $response = getJson(route('payments.associated', ['payment' => Payment::first()->id]))
    //    ->assertOk();

    $this->assertDatabaseCount('payments', 2);
    $this->assertDatabaseHas('invoices', ['paid_status' => Invoice::STATUS_PAID]);
});

test('invoice payment with fees must be from same gateway type', function (): void {
    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $fees = PaymentGatewaysFee::factory()
        ->times(2)
        ->sequence(['amount' => 100, 'type' => 'fixed'], ['amount' => 150, 'type' => 'percentage'])
        ->create([
            'payment_gateway' => 'Authorize',
            'company_id' => $user->company_id,
        ]);

    Log::debug('Ids: ?', $fees->pluck('id')->toArray());

    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, ['invoices' => [$invoice->id, $invoiceAlt->id], 'amount' => 250]);
    $data = array_merge($data, ['has_fees' => true, 'fees' => $fees->pluck('id')->toArray()]);

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertStatus(422)->assertJsonFragment([
            'All the payment fees must be from the same payment gateway type.',
        ]);
    Log::debug($response->json());

});

test('all invoice payment fees must be valid id', function (): void {
    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $fees = PaymentGatewaysFee::factory()
        ->times(2)
        ->sequence(['amount' => 100, 'type' => 'fixed'], ['amount' => 150, 'type' => 'percentage'])
        ->create([
            'payment_gateway' => 'Authorize',
            'company_id' => $user->company_id,
        ]);

    $ids = array_merge($fees->pluck('id')->toArray(), [0]);

    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, ['invoices' => [$invoice->id, $invoiceAlt->id], 'amount' => 250]);
    $data = array_merge($data, ['has_fees' => true, 'fees' => $ids]);

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertStatus(422)->assertJsonFragment([
            'The payment fees id are not valid.',
        ]);
    Log::debug($response->json());

});

test('invoice payment with multiple fees works', function (): void {

    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $fees[0] = PaymentGatewaysFee::factory()->create([
        'payment_gateway' => PaymentGateways::NAME_AUX_VAULT,
        'type' => PaymentGatewaysFee::TYPE_FIXED,
        'amount' => 100,
        'aux_vault_setting_id' => AuxVaultSetting::first()->id,
        'company_id' => $user->company_id,
        'name' => PaymentGateways::NAME_AUX_VAULT,
    ]);

    $fees[1] = PaymentGatewaysFee::factory()->create([
        'payment_gateway' => PaymentGateways::NAME_AUX_VAULT,
        'type' => PaymentGatewaysFee::TYPE_PERCENTAGE,
        'amount' => 20,
        'aux_vault_setting_id' => AuxVaultSetting::first()->id,
        'company_id' => $user->company_id,
        'name' => PaymentGateways::NAME_AUX_VAULT,
    ]);

    $extraData = [
        'has_fees' => 1,
        'fees' => [$fees[0]['id'],$fees[1]['id']],
        'invoices' => [$invoice->id, $invoiceAlt->id],
        'amount' => 250
    ];
    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, $extraData);

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertOk();

    Log::debug($response->json());
    Log::debug('Data', [
        'aux_vault' => AuxVault::first()->toArray() ?? [],
        'fees' => TransactionFees::get()->toArray() ?? [],
        'invoice' => Invoice::first()->toArray() ?? [],
        'payments' => Payment::get()->toArray() ?? []
    ]);

    $this->assertDatabaseCount('payments', 2);
    $this->assertDatabaseHas('invoices', ['paid_status' => Invoice::STATUS_PAID]);
    $this->assertDatabaseHas('transaction_fees', ['total' => 100]);
    $this->assertDatabaseHas('transaction_fees', ['total' => 50]);
    $this->assertDatabaseHas('aux_vaults', ['amount' => 4.00]);

});

test('it can pay partial', function (): void {
    Bus::fake();
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create([
            'status_customer' => 'A',
            'status_payment' => 'prepaid',
            'role' => 'customer',
            'auto_debit' => '1',
            'minimun_balance' => 3,
            'auto_replenish_amount' => 4,
            'balance' => 4,
        ]);
    generatePaymentData($user);
    Sanctum::actingAs($user);
    $invoice = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 150,
    ]);
    $invoiceAlt = Invoice::factory()->create([
        'user_id' => $user->id,
        'due_amount' => 100,
    ]);
    $paymentAccount = PaymentAccount::first();

    $data = (PaymentAccountData::fromModel($paymentAccount))->toArray();
    $data = array_merge($data, ['invoices' => [$invoice->id, $invoiceAlt->id], 'amount' => 230]);

    $response = postJson(route('generic.multi-payment'), $data)
        ->assertOk();
    Log::debug($response->json());
    Log::debug('aux vault: ', AuxVault::all()->toArray());
    Log::debug('Payments:  ', Payment::all()->toArray());


    $this->assertDatabaseCount('payments', 2);
    $this->assertDatabaseHas('invoices', ['paid_status' => Invoice::STATUS_PAID]);
    $this->assertDatabaseHas('invoices', ['paid_status' => Invoice::STATUS_PARTIALLY_PAID]);
    $this->assertThat(Payment::sum('amount'), $this->equalTo(230));
    $this->assertThat(Invoice::sum('due_amount'), $this->equalTo(20));


});
