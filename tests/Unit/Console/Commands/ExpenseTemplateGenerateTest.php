<?php

use Crater\Models\Expense;
use Crater\Models\ExpenseTemplate;
use Crater\Models\Item;
use Crater\Models\User;

it('wont work when there is still time for the term', function (string $term, string $date) {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => $date,
        'term'                    => $term,
        'last_date'               => $date,
    ]));

    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(0);
}->with([
    'daily' => [1, 2, 3],
    'negative numbers' => [-1, -2, -3],
    'using closure' => [fn () => ExpenseTemplate::TERM_BIMONTLY, now()->subMonths(1)->format('Y-m-d')],
]);

it('wont work when the period is past the date', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonths(2)->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'last_date'               => now()->subMonths(2)->format('Y-m-d'),
    ]));

    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(0);
});

it('use the expense_date when last_date is empty', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonth()->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'last_date'               => null,
    ]));


    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(1)
        ->and(ExpenseTemplate::first())->last_date->toBe(now()->format('Y-m-d'));
});

it('store all the data correctly for pending', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonth()->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'initial_status'          => Expense::STATUS_PENDING,
        'template_expense_number' => '09001728888',
    ]));

    $this->artisan('expense:template-generate');

    $expense = Expense::first();
    $template = ExpenseTemplate::first();

    expect($expense)->status->toBe($template->initial_status)
        ->expense_date->toBe(now()->addDays($template->days_after_payment_date)->format('Y-m-d'))
        ->notification->toBe($template->notification)
        ->items_id->toBe($template->items_id)
        ->company_id->toBe($template->company_id)
        ->expense_number->toBe($expense->expense_prefix . '-000001')
        ->expense_category_id->toBe($template->expense_category_id)
        ->and($template->last_date)->toBe(now()->format('Y-m-d'));
});

it('store all the data correctly for active', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonth()->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'initial_status'          => Expense::STATUS_ACTIVE,
        'template_expense_number' => '09001728888',
    ]));

    $this->artisan('expense:template-generate');

    $expense = Expense::first();
    $template = ExpenseTemplate::first();

    expect($expense)->status->toBe($template->initial_status)
        ->expense_date->toBe(now()->format('Y-m-d'))
        ->expense_number->toBe($expense->expense_prefix . '-000001');
});

it('store all the data correctly for active daily', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subDay()->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_DAILY,
        'initial_status'          => Expense::STATUS_ACTIVE,
        'template_expense_number' => '09001728888',
    ]));

    $this->artisan('expense:template-generate');

    $expense = Expense::first();
    $template = ExpenseTemplate::first();

    expect($expense)->status->toBe($template->initial_status)
        ->expense_date->toBe(now()->format('Y-m-d'))
        ->expense_number->toBe($expense->expense_prefix . '-000001');
});

it('use last date when it exist', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonths(2)->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'last_date'               => now()->subMonth()->format('Y-m-d'),
    ]));

    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(1);
});

//it('work well when created in day 31', function () {
// Need php 8+ to test
//SELECT DATE_ADD('2023-01-31', INTERVAL 1 MONTH)
//})->skip();

it('wont try again', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonths(2)->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'last_date'               => now()->subDay()->format('Y-m-d'),
    ]));

    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(0);
});


it('generate the due_date for active', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonths(2)->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'last_date'               => now()->subMonth()->format('Y-m-d'),
        'initial_status'          => Expense::STATUS_ACTIVE,
    ]));

    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(1)->and(Expense::first())->expense_date->toBe(now()->format('Y-m-d'));
});

it('generate the due_date for pending', function () {

    $data = getConfig();

    ExpenseTemplate::factory()->create(array_merge($data, [
        'status'                  => ExpenseTemplate::STATUS_ACTIVE,
        'days_after_payment_date' => 3,
        'expense_date'            => now()->subMonths(2)->format('Y-m-d'),
        'term'                    => ExpenseTemplate::TERM_MONTLY,
        'last_date'               => now()->subMonth()->format('Y-m-d'),
        'initial_status'          => Expense::STATUS_PENDING,
    ]));

    $this->artisan('expense:template-generate');

    expect(Expense::count())->toBe(1)
        ->and(Expense::first())->expense_date->toBe(now()->addDays(3)->format('Y-m-d'));
});

function getConfig(): array
{
    $user = User::factory()->forCompany()->create();
    /* @var User $user */
    $user->company->settings()->create([
        'option' => 'job_expense_template_enable',
        'value'  => 1,
    ]);
    $user->company->settings()->create([
        'option' => 'job_expense_template_time_run',
        'value'  => now()->format('H:i'),
    ]);
    return [
        'company_id'   => $user->company_id,
        'items_id'     => Item::factory()->create()->id,
        'notification' => 1,
    ];

    //return $user;
}
