<?php

namespace Database\Factories;

use Crater\Models\TransactionFees;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class TransactionFeesFactory extends Factory
{
    protected $model = TransactionFees::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
