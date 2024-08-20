<?php

use Crater\Models\EstimateTemplate;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $estimate_templates_results = count(EstimateTemplate::get()->pluck('id')->toArray());

        if ($estimate_templates_results == 0) {
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

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        //
    }
};
