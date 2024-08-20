<?php

namespace Database\Factories;

use Crater\Models\Company;
use Crater\Models\ProfileDID;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileDIDFactory extends Factory
{
    protected $model = ProfileDID::class;

    public function definition(): array
    {

        return [
            'did_rate' => 0.25,
            'name' => 'did_',
            'company_id' => Company::first()->id ?? null,
            'did_number' => '123456',
            'unmetered' => 0,
        ];
    }
}
