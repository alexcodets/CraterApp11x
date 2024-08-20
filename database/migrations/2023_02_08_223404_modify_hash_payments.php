<?php

use Crater\Helpers\General;
use Crater\Models\Payment;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $hashs = Payment::pluck('unique_hash');
        foreach ($hashs as $hash) {
            $count_hash = Payment::where('unique_hash', $hash)->count();
            if($count_hash > 1) {
                $payment = Payment::where('unique_hash', $hash)->latest()->first();
                $payment->unique_hash = General::generateUniqueId();
                $payment->save();
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
