<?php

use Crater\Models\Company;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $companies = Company::all();

        if ($companies->isEmpty()) {
            return;
        }

        foreach ($companies as $company) {

            $company->settings()->updateOrCreate(
                ['option' => 'retention_active'],
                ['value' => 'NO']
            );

            $company->settings()->updateOrCreate(
                ['option' => 'retention_platform_active'],
                ['value' => 'NO']
            );

        }

    }
};
