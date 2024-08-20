<?php

use Carbon\Carbon;
use Crater\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {

        if (Company::first() != null) {
            DB::table('company_settings')->insert([
                'option' => 'invoice_late_fee_retroactive',
                'value' => "0",
                'company_id' => Company::first()->id,
                'created_at' => Carbon::now(),
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
