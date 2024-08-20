<?php

use Crater\Http\Requests\ProfileDidTollFree;
use Crater\Models\PbxServicesDID;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $pbxServiceDids = PbxServicesDID::whereNull('deleted_at')->whereNull('price')->whereNotNull('custom_did_id');

        foreach ($pbxServiceDids as $did) {

            $service = $did->service;
            if (! is_null($service)) {
                if (! is_null($service->pbxPackage)) {
                    if (! is_null($service->pbxPackage->profileDid)) {
                        if(! is_null($service->pbxPackage->profileDid->did_rate)) {
                            $did->price = $service->pbxPackage->profileDid->did_rate;
                            $did->save();
                        }
                    }
                }
            }
            $tollFrees = ProfileDidTollFree::where('id', $did->custom_did_id);
            if(! is_null($tollFrees)) {
                $did->price = $tollFrees->rate_per_minute;
                $did->name_prefix = "Custom";
                $did->save();
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
