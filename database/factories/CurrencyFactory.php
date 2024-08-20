<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CurrencyFactory extends Factory
{
    public function definition(): array
    {
        return             [
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
            'precision' => '2',
            'thousand_separator' => ',',
            'decimal_separator' => '.',
        ];
    }
}
