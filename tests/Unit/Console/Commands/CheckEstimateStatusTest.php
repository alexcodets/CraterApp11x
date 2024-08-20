<?php

use Crater\Models\Estimate;

it('work', function () {

    Estimate::factory()->times(5)->create([
        'status' => Estimate::STATUS_VIEWED,
    ]);
    $this->artisan('estimate:check_status');

    expect(Estimate::where('status', Estimate::STATUS_EXPIRED)->count())->toBe(5);
});

it('work for right status', function () {

    Estimate::factory()->times(2)->create([
        'status' => Estimate::STATUS_VIEWED,
    ]);
    Estimate::factory()->times(4)->create([
        'status' => Estimate::STATUS_ACCEPTED,
    ]);
    Estimate::factory()->times(8)->create([
        'status' => Estimate::STATUS_REJECTED,
    ]);
    $this->artisan('estimate:check_status');
    //'Y-m-d'
    expect(Estimate::where('status', Estimate::STATUS_EXPIRED)->count())->toBe(2);
});

it('work for right date', function () {

    Estimate::factory()->times(2)->create([
        'status' => Estimate::STATUS_VIEWED,
        'expiry_date' => now()->addDay()->format('Y-m-d')
    ]);

    Estimate::factory()->times(4)->create([
        'status' => Estimate::STATUS_VIEWED,
        'expiry_date' => now()->subDay()->format('Y-m-d')
    ]);

    Estimate::factory()->times(8)->create([
        'status' => Estimate::STATUS_VIEWED,
        'expiry_date' => now()->format('Y-m-d')
    ]);

    $this->artisan('estimate:check_status');
    expect(Estimate::where('status', Estimate::STATUS_EXPIRED)->count())->toBe(4);
});
