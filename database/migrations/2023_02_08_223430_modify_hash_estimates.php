<?php

use Crater\Helpers\General;
use Crater\Models\Estimate;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $hashs = Estimate::pluck('unique_hash');
        foreach ($hashs as $hash) {
            $count_hash = Estimate::where('unique_hash', $hash)->count();
            if($count_hash > 1) {
                $estimate = Estimate::where('unique_hash', $hash)->latest()->first();
                $estimate->unique_hash = General::generateUniqueId();
                $estimate->save();
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
