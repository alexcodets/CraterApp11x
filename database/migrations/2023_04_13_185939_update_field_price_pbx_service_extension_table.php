<?php

use Crater\Models\PbxServicesExtensions;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $pbxServiceExtensions = PbxServicesExtensions::whereNull('deleted_at')->whereNull('price')->get();
        ;

        foreach ($pbxServiceExtensions as $extension) {
            $pbxService = $extension->service;
            if (! is_null($pbxService)) {
                if (! is_null($pbxService->pbxPackage)) {
                    if (! is_null($pbxService->pbxPackage->profileExtensions)) {
                        if (! is_null($pbxService->pbxPackage->profileExtensions->rate)) {
                            $extension->price = $pbxService->pbxPackage->profileExtensions->rate;
                            $extension->save();
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
