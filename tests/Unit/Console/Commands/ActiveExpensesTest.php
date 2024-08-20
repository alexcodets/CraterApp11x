<?php

use Crater\Models\Expense;

it('work', function () {
    $date = now();
    Expense::factory()->times(15)->create([
        'expense_date' => now()->subDays(2)->format('Y-m-d'),
        'status' => Expense::STATUS_PENDING,
    ]);

    $this->artisan('expense:activation');
    expect(Expense::where('status', Expense::STATUS_ACTIVE)->count())->toBe(15);
});

it('works on right date', function () {
    $date = now();
    Expense::factory()->times(10)->create([
        'expense_date' => now()->subDays(2)->format('Y-m-d'),
        'status' => Expense::STATUS_PENDING,
    ]);

    Expense::factory()->times(5)->create([
        'expense_date' => now()->format('Y-m-d'),
        'status' => Expense::STATUS_PENDING,
    ]);

    Expense::factory()->times(5)->create([
        'expense_date' => now()->addDays(2)->format('Y-m-d'),
        'status' => Expense::STATUS_PENDING,
    ]);

    $this->artisan('expense:activation');
    expect(Expense::where('status', Expense::STATUS_ACTIVE)->count())->toBe(15)
        ->and(Expense::where('status', Expense::STATUS_PENDING)->count())->toBe(5);

});
