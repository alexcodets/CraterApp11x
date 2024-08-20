<?php

namespace Database\Factories;

use Crater\Models\Company;
use Crater\Models\PbxServers;
use Crater\Models\PbxServerTenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PbxServerTenantFactory extends Factory
{
    protected $model = PbxServerTenant::class;

    public function definition(): array
    {
        return [
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'name' => $this->faker->name(),
            'tenant_code' => $this->faker->randomNumber(3, true),
            'tenant_id' => $this->faker->randomNumber(4),
            'status' => 2,
            'pbx_server_id' => PbxServers::first()->id ?? null,
            'creator_id' => $this->faker->randomNumber(),
            'company_id' => Company::first()->id ?? null,
        ];
    }
}
