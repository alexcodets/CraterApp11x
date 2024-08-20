<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('pbx_services', function (Blueprint $table) {
            $table->boolean('first_time_import')->default(1);
        });

        $pbxServices = \Crater\Models\PbxServices::all();

        foreach ($pbxServices as $item) {
            if ($item->jobLogs()->count() > 0) {
                $item->first_time_import = false;
                $item->save();
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
        Schema::table('pbx_services', function (Blueprint $table) {
            //
        });
    }
};
