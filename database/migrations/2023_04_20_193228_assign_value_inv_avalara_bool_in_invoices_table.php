<?php

use Crater\Models\AvalaraInvoice;
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
        $ids_avalara_invoice = AvalaraInvoice::pluck('invoice_id');

        if($ids_avalara_invoice->isNotEmpty()) {
            foreach ($ids_avalara_invoice as $id) {
                $invoice = Invoice::find($id);
                if($invoice) {
                    $invoice->inv_avalara_bool = 1;
                    $invoice->save();
                }
            }

        }

    }

    public function down(): void
    {
        //down
    }
};
