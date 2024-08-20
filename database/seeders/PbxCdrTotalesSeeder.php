<?php

namespace Database\Seeders;

use Crater\Models\PbxServices;
use Crater\Pbxware\Service\PbxService;
use Illuminate\Database\Seeder;

class PbxCdrTotalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $service = new PbxService();
        $service->calculateTotal(PbxServices::first());
    }
}
