<?php

namespace Database\Seeders;

use Crater\Models\CustomRate;
use Crater\Models\CustomRateGroup;
use Crater\Models\CustomRateGroupItems;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class InternationalRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        CustomRateGroup::create([
            'company_id' => 1,
            'name' => 'Inbound Group',
            'status' => 'A',
            'type' => 'Inbound',
        ]);

        CustomRateGroup::create([
            'company_id' => 1,
            'name' => 'Outbound Group',
            'status' => 'A',
            'type' => 'Outbound',
        ]);

        $rates = new CustomRate();
        $items = json_decode(Storage::disk('seed')->get('international-rate-in.json'), true);

        foreach (array_chunk($items, 250) as $values) {
            $rates->insert($values);
        }

        $totalRates = CustomRate::count();
        for ($i = 0; $i < $totalRates; $i++) {
            CustomRateGroupItems::create([
                'company_id' => 1,
                'prefixrate_id' => 1,
                'int_rate_id' => 1 + $i,
            ]);
        }

        $items = json_decode(Storage::disk('seed')->get('international-rate-out.json'), true);

        foreach (array_chunk($items, 250) as $values) {
            $rates->insert($values);
        }

        $totalRates = CustomRate::count();
        for ($e = $i; $e <= $totalRates; $e++) {
            CustomRateGroupItems::create([
                'company_id' => 1,
                'prefixrate_id' => 2,
                'int_rate_id' => 1 + $e,
            ]);
        }


    }
}
