<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class ModuleMobileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // insert new data
        DB::table('modules')->insert(
            [
                'name' => 'Billpay',
                'description' => 'Billpay module',
                'version' => '1.0',
                'image' => '/images/billpay.jpg',
                'status' => 'A',
                'slug' => 'admin/module/billpay',
                'company_id' => 1,
                'creator_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        );
    }
}
