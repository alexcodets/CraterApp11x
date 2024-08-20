<?php

namespace Database\Seeders;

use Crater\Models\CompanySetting;
use Illuminate\Database\Seeder;

class ExtStatusSendEmailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        CompanySetting::insert([
            [
                'option' => 'pbx_ext_subject_down',
                'value' => 'admin@craterapp.com',
                'company_id' => 1
            ],
            [
                'option' => 'pbx_ext_body_down',
                'value' => 'La extension esta caida.',
                'company_id' => 1
            ],
            [
                'option' => 'pbx_ext_subject_up',
                'value' => 'admin@craterapp.com',
                'company_id' => 1
            ],
            [
                'option' => 'pbx_ext_body_up',
                'value' => 'La extension esta up.',
                'company_id' => 1
            ],
            [
                'option' => 'color_invoice',
                'value' => '#5851D8',
                'company_id' => 1
            ],
        ]);
    }
}
