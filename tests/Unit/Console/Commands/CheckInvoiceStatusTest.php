<?php

use Crater\Models\Invoice;

it('works', function () {

    Invoice::Factory()->times(2)->create([
        'status' => Invoice::STATUS_VIEWED,
        'due_date' => now()->subDay(),
    ]);

    $this->artisan('invoices:check_status');

    expect(Invoice::where('status', Invoice::STATUS_OVERDUE)->count())->toBe(2);
});

it('wont work in the wrong status', function () {

    Invoice::Factory()->times(2)->create([
        'status' => Invoice::STATUS_COMPLETED,
        'due_date' => now()->subDay(),
    ]);

    $this->artisan('invoices:check_status');

    expect(Invoice::where('status', Invoice::STATUS_OVERDUE)->count())->toBe(0);
});

it('works only in the right date', function () {

    Invoice::Factory()->times(2)->create([
        'status' => Invoice::STATUS_VIEWED,
        'due_date' => now()->subDay(),
    ]);
    Invoice::Factory()->times(4)->create([
        'status' => Invoice::STATUS_VIEWED,
        'due_date' => now(),
    ]);
    Invoice::Factory()->times(8)->create([
        'status' => Invoice::STATUS_VIEWED,
        'due_date' => now()->addDay(),
    ]);

    $this->artisan('invoices:check_status');

    expect(Invoice::where('status', Invoice::STATUS_OVERDUE)->count())->toBe(2);
});
