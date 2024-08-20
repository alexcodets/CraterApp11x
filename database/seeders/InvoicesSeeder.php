<?php

namespace Database\Seeders;

use Crater\Models\Invoice;
use Illuminate\Database\Seeder;

class InvoicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        Invoice::factory()->count(50)->create();
    }
}
