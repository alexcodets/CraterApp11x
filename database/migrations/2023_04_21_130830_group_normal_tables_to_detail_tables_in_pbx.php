<?php

use Crater\Models\Invoice;
use Crater\Models\InvoiceDid;
use Crater\Models\InvoiceExtension;
use Illuminate\Database\Migrations\Migration;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        $invoices_ids = Invoice::where("pbx_service_id", "!=", null)->whereNull('deleted_at')->pluck("id");


        if($invoices_ids->isNotEmpty()) {
            foreach ($invoices_ids as $invoice_id) {
                // Extensions_group
                $extensions_group = DB::table('invoice_extensions')->where("invoice_id", $invoice_id)
                ->selectRaw('template_extension_name as name, count(*) as quantity, template_extension_rate as price')
                ->groupBy("template_extension_rate")->get();

                $is_exist_EXTENSION = DB::table('invoice_pbx_extension_detail')->where('invoice_id', $invoice_id)->get();

                if($is_exist_EXTENSION->isEmpty()) {
                    foreach ($extensions_group as $group) {
                        DB::table('invoice_pbx_extension_detail')->insert([
                            'invoice_id' => $invoice_id,
                            'name' => $group->name,
                            'quantity' => $group->quantity,
                            'price' => $group->price,
                            'total' => $group->quantity * $group->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                }

                // Dids_groups
                $dids_group = DB::table('invoice_dids')->where("invoice_id", $invoice_id)
                ->selectRaw('name_prefix as name, count(*) as quantity, price')
                ->groupBy("name_prefix", "price")->get();

                $is_exist_DID = DB::table('invoice_pbx_did_detail')->where('invoice_id', $invoice_id)->get();

                if($is_exist_DID->isEmpty()) {
                    foreach ($dids_group as $group) {
                        DB::table('invoice_pbx_did_detail')->insert([
                            'invoice_id' => $invoice_id,
                            'name' => $group->name != null ? $group->name : "DID template",
                            'quantity' => $group->quantity,
                            'price' => $group->price,
                            'total' => $group->quantity * $group->price,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'deleted_at' => null,
                        ]);
                    }
                }

                //count_extension && count_did
                $invoice = Invoice::find($invoice_id);

                if($invoice) {
                    $count_invoice_extension = InvoiceExtension::where("invoice_id", $invoice_id)->count();
                    $count_invoice_did = InvoiceDid::where("invoice_id", $invoice_id)->count();

                    $invoice->count_extension = $count_invoice_extension;
                    $invoice->count_did = $count_invoice_did;
                    $invoice->save();
                }

            }
        }
    }

    public function down(): void
    {
        // down
    }
};
