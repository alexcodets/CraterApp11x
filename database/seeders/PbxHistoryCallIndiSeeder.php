<?php

namespace Database\Seeders;

use Crater\Models\CallHistoryIndi;
use Illuminate\Database\Seeder;

class PbxHistoryCallIndiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $time = now()->subHours(2);
        CallHistoryIndi::factory()->count(3)->create(
            [
                'created_at' => $time,
                'updated_at' => $time,
            ]
        );
        $time->addHours(1);
        CallHistoryIndi::factory()->count(3)->create(
            [
                'created_at' => $time,
                'updated_at' => $time,
            ]
        );

        $time->addHours(1);
        CallHistoryIndi::factory()->count(14)->create(
            [
                'created_at' => $time,
                'updated_at' => $time,
            ]
        );
    }
}
