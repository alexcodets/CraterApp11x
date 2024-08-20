<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        //

        DB::table('modules')->delete();
        $modules = [

            ['name' => 'PBXware', 'description' => '', 'version' => '1.0','image' => '/images/corepbx.png','status' => 'A','slug' => 'admin/settings/pbx','company_id' => 1,'creator_id' => 1],
            ['name' => 'Avalara', 'description' => 'Explore scalable solutions to help with sales tax returns, registrations, and calculations.', 'version' => '1.0','image' => '/images/avalara.jpg','status' => 'A','slug' => 'admin/avalara/configs','company_id' => 1,'creator_id' => 1],
            ['name' => 'BillPay', 'description' => 'Billpay module', 'version' => '1.0','image' => '/images/billpay.jpg','status' => 'A','slug' => 'admin/module/billpay','company_id' => 1,'creator_id' => 1],
            ['name' => 'Bandwidth', 'description' => 'Bandwidth module', 'version' => '1.0', 'image' => '/images/bandwidth.jpg', 'status' => 'A', 'slug' => 'admin/settings/bandwidth', 'company_id' => 1, 'creator_id' => 1],
            ['name' => 'corePOS', 'description' => 'Module corebill POS', 'version' => '1.0', 'image' => '/images/corePos.jpg', 'status' => 'A', 'slug' => 'admin/module/corePOS', 'company_id' => 1, 'creator_id' => 1]
        ];

        $id = 0;

        foreach ($modules as $module) {
            $id++;
            $module['id'] = $id;
            DB::table('modules')->insert($module);
        }
    }
}
