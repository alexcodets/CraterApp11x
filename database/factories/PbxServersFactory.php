<?php

namespace Database\Factories;

use Crater\Models\PbxServers;
use Crater\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PbxServersFactory extends Factory
{
    protected $model = PbxServers::class;

    public function definition(): array
    {
        $user = User::first();

        return [
            'server_label' => $this->faker->name(),
            'hostname' => config('pbxware.dev.base_url'),
            'ssl_port' => 443,
            'api_key' => config('pbxware.dev.private_key'),
            'national_dialing_code' => '1',
            'international_dialing_code' => '+1',
            'company_id' => $user->company_id ?? null,
            'creator_id' => $user->id ?? null,
        ];
    }
}
