<?php

use Crater\Models\AdditionalCharge;
use Crater\Models\InvoiceAdditionalCharge;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        try {
            DB::beginTransaction();
            //code...
            $additionalCharges = InvoiceAdditionalCharge::where('profile_extension_id', null)
                ->where('profile_did_id', null)->get();

            foreach ($additionalCharges as $value) {
                $additionalCharges = AdditionalCharge::where('id', $value->additional_charge_id)->first();
                $value->profile_extension_id = $additionalCharges->profile_extension_id;
                $value->profile_did_id = $additionalCharges->profile_did_id;
                $value->save();
            }
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Migration Additional Charges', ['error' => $th]);
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
