<?php

use Crater\Models\Company;
use Crater\Models\Modules;
use Crater\Models\User;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $company = Company::first();
        $user = User::first();
        Modules::create([
            'name' => 'corePOS',
            'description' => 'Module corebill POS',
            'version' => '1.0',
            'image' => '/images/corePos.jpg',
            'status' => 'A',
            'slug' => 'admin/module/corePOS',
            'company_id' => $company->id ?? null,
            'user_id' => $user->id ?? null,

        ]);
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
