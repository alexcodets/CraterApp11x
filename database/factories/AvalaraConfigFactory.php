<?php

namespace Database\Factories;

use Crater\Models\AvalaraConfig;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvalaraConfigFactory extends Factory
{
    protected $model = AvalaraConfig::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $env = config('avalara.environment');
        $apiData = config("avalara.{$env}");

        return [
            'user_name' => $apiData['user_name'] ?? '',
            'password' => $apiData['password'] ?? '',
            'client_id' => $apiData['client_id'] ?? '',
            'url' => $apiData['url'] ?? '',
            'host' => $apiData['host'] ?? '',
            'conexion' => 'TheConexion',
            'bscl' => '1',
            'svcl' => '1',
            'fclt' => 1,
            'reg' => 1,
            'frch' => 1,
            'lfln' => 1,
            'dtl' => 1,
            'summ' => 1,
            'retnb' => 1,
            'retext' => 1,
            'incrf' => 1,
            'account_reference' => 123,
            'company_identifier' => 321,
        ];
    }
}
