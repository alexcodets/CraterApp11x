<?php

use Crater\Models\Company;
use Crater\Models\CompanySetting;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Obtener todos los IDs de la tabla companies
        $companyIds = Company::pluck('id');

        foreach ($companyIds as $companyId) {
            // Verificar si existe el valor 'invoice_issuance_period' en company_settings para el company_id
            $settingExists = CompanySetting::where('company_id', $companyId)
                ->where('option', 'invoice_issuance_period')
                ->exists();

            // Si no existe, agregar un nuevo registro en company_settings
            if (! $settingExists) {
                CompanySetting::create([
                    'company_id' => $companyId,
                    'option' => 'invoice_issuance_period',
                    'value' => '7', // Asegúrate de que el tipo de dato de 'value' sea compatible con este valor
                ]);
            }
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
