<?php

use Crater\Helpers\General;
use Crater\Models\Invoice;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $hashs = Invoice::pluck('unique_hash');
        foreach ($hashs as $hash) {
            $count_hash = Invoice::where('unique_hash', $hash)->count();
            if($count_hash > 1) {
                $invoice = Invoice::where('unique_hash', $hash)->latest()->first();
                $invoice->unique_hash = General::generateUniqueId();
                $invoice->save();
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
