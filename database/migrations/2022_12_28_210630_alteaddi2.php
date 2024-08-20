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
        //
        try {
            DB::beginTransaction();
            //code...
            $additionalCharges = InvoiceAdditionalCharge::whereNULL('profile_extension_id')
                ->whereNULL('profile_did_id')->where('additional_charge_id', 0)->get();

            foreach ($additionalCharges as $value) {

                $additionalCharges = AdditionalCharge::where('description', $value->additional_charge_name)->first();
                if ($additionalCharges != null) {
                    $value->profile_extension_id = $additionalCharges->profile_extension_id;
                    $value->profile_did_id = $additionalCharges->profile_did_id;
                    $value->save();
                }
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
