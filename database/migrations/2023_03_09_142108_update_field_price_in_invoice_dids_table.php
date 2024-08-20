<?php

use Crater\Models\InvoiceDid;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $invoice_dids_ids = InvoiceDid::all()->pluck("id");
        foreach ($invoice_dids_ids as $i => $id) {
            $invoice_did = InvoiceDid::find($id);

            if($invoice_did->custom_did_id != null) {
                $invoice_did->price = $invoice_did->custom_did_rate;
                $invoice_did->save();
            }
            if($invoice_did->template_did_id != null) {
                $invoice_did->price = $invoice_did->template_did_rate;
                $invoice_did->save();
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
