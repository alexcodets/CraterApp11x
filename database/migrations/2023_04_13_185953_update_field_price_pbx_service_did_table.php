<?php

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
        $pbxServiceDids = PbxServicesDID::whereNull('deleted_at')->whereNull('price')->whereNull('custom_did_id')->get();

        foreach ($pbxServiceDids as $did) {
            $service = $did->service;
            if (! is_null($service)) {
                if (! is_null($service->pbxPackage)) {
                    if (! is_null($service->pbxPackage->profileDid)) {
                        if(! is_null($service->pbxPackage->profileDid->did_rate)) {
                            $did->price = $service->pbxPackage->profileDid->did_rate;
                            $did->name_prefix = "Template";
                            $did->save();
                        }
                    }
                }
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
