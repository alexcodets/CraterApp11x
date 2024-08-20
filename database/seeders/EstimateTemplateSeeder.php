<?php

namespace Database\Seeders;

use Crater\Models\EstimateTemplate;
use Illuminate\Database\Seeder;

class EstimateTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        EstimateTemplate::create([
            'name' => 'Template 1',
            'view' => 'estimate1',
            'path' => '/assets/img/PDF/Template1.png'
        ]);

        EstimateTemplate::create([
            'name' => 'Template 2',
            'view' => 'estimate2',
            'path' => '/assets/img/PDF/Template2.png'
        ]);

        EstimateTemplate::create([
            'name' => 'Template 3',
            'view' => 'estimate3',
            'path' => '/assets/img/PDF/Template3.png'
        ]);
    }
}
