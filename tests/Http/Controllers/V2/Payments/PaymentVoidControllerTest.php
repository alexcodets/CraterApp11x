<?php

namespace Tests\Http\Controllers\V2\Payments;

use Crater\Models\{Company, Currency, Invoice, Payment, PaymentAccount, PaymentDevolution, PaymentMethod, User};
use Crater\Traits\FastMigrationTrait;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Laravel\Sanctum\Sanctum;

use function Pest\Laravel\postJson;

//uses(FastMigrationTrait::class);
uses(RefreshDatabase::class);

test('auth is required', function () {
    Bus::fake();

    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create();
    $invoice = Invoice::factory()->create(
        ['company_id' => $user->company_id, 'user_id' => $user->id]
    );
    $payment = Payment::factory()->create(
        ['invoice_id' => $invoice->id, 'user_id' => $user->id, 'company_id' => $user->company_id]
    );
    postJson(route('generic.void', ['payment' => $payment]))->assertStatus(401);
});

it('can void a invoice', function () {
    Bus::fake();

    $user = generatePaymentData();
    Sanctum::actingAs($user);

    $invoice = Invoice::factory()->create(
        ['company_id' => $user->company_id, 'user_id' => $user->id]
    );
    $payment = Payment::factory()->create(
        [
            'invoice_id' => $invoice->id,
            'user_id' => $user->id,
            'company_id' => $user->company_id,
            'payment_method_id' => PaymentMethod::first()->id,
        ]
    );
    $response = postJson(route('generic.void', ['payment' => $payment]), ['only_local' => true])
        ->assertStatus(200)
        ->assertJsonFragment(['payments_id' => [$payment->id]]);
    //\Log::debug('Response is: ', $response->json());
});

it('can void multiple invoice', function () {
    Bus::fake();

    $user = generatePaymentData();
    Sanctum::actingAs($user);
    $invoices = Invoice::factory()
        ->times(2)
        ->state(new Sequence(
            ['due_amount' => 125, 'total' => 125, 'invoice_number' => 'INV-000001'],
            ['due_amount' => 142, 'total' => 142, 'invoice_number' => 'INV-000002'],
        ))
        ->create(
            ['company_id' => $user->company_id, 'user_id' => $user->id, 'paid_status' => Invoice::STATUS_PAID]
        );
    \Log::debug('invoices: ', $invoices->toArray());
    $payments = Payment::factory()->times(2)
        ->state(new Sequence(
            ['invoice_id' => $invoices[0], 'amount' => 125],
            ['invoice_id' => $invoices[1], 'amount' => 142],
        ))
        ->create(
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'payment_method_id' => PaymentMethod::first()->id,
                'authorize_id' => 12345679,
            ]
        );
    \Log::debug('Payments Before: ', Payment::get()->toArray());
    $response = postJson(route('generic.void', ['payment' => $payments[0]]), ['only_local' => true])
        ->assertStatus(200)
        ->assertJsonFragment(['payments_id' => $payments->pluck('id')->toArray()]);
    $this->assertDatabaseCount('payment_devolutions', 2);
    \Log::debug('Payments After: ', Payment::get()->toArray());
    \Log::debug('invoices after: ', Invoice::get()->toArray());


    expect(Invoice::where('paid_status', Invoice::STATUS_PAID)->count())->toBe(0)
        ->and(PaymentDevolution::count())->toBe(2)
        ->and(Invoice::where('paid_status', Invoice::STATUS_UNPAID)->count())->toBe(2);
});

function generatePaymentData(): User
{
    $user = User::factory()
        ->for(Company::factory())
        ->for(Currency::factory())
        ->create();

    PaymentAccount::factory()->create([
        'client_id' => $user->id,
        'payment_account_type' => 'CC',
    ]);
    PaymentMethod::factory()->create([
        'status' => 'A',
        'account_accepted' => 'C',
    ]);

    return $user;

}
