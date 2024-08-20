<?php

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
        $invoices = Invoice::where('pbx_service_id', '!=', null)
                            ->where('count_extension', '>', 0)
                            ->where('pbx_extension_price', 0.00)->get();

        if(count($invoices) > 0) {
            foreach ($invoices as $invoice) {
                $division = $invoice->pbx_total_extension / 100;
                $value_pbx_extension_price = $division / $invoice->count_extension;
                $invoice->pbx_extension_price = $value_pbx_extension_price;
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
