<?php

namespace Database\Seeders;

use Crater\Models\CallDetailRegisterTotal;
use Crater\Models\Invoice;
use Crater\Models\InvoiceAdditionalCharge;
use Crater\Models\InvoiceDid;
use Crater\Models\InvoiceExtension;
use Crater\Models\InvoiceItem;
use Crater\Models\InvoiceTemplate;
use Crater\Models\Item;
use Crater\Models\PbxDID;
use Crater\Models\PbxExtensions;
use Crater\Models\PbxServices;
use Crater\Models\User;
use Illuminate\Database\Seeder;
use Storage;

class AvalaraInvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $service = PbxServices::first();
        $clientData = json_decode(Storage::disk('seed')->get('client.json'), true);
        $user = User::where('email', $clientData[0]['client']['email'])->first();

        if (is_null($user)) {
            return;
        }

        $cdrs = ($service->pbxCdrTotals()->sum('exclusive_cost') + $service->pbxPackage->rate) * 100;
        $dids = 2494;
        $extensions = 3100;
        $items = 5010;

        /* @var Invoice $invoice */
        $invoice = Invoice::factory()
            ->has(InvoiceAdditionalCharge::factory(['company_id' => $user->company_id, 'creator_id' => $user->id])->times(3))
            ->create([
                'pbx_service_id' => $service->id,
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'pbx_total_items' => $items,
                'pbx_total_extension' => $extensions,
                'pbx_total_did' => $dids,
                'pbx_total_cdr' => $cdrs,
                'sub_total' => $cdrs + $dids + $extensions + $items,
                'total' => $cdrs + $dids + $extensions + $items,
                'discount' => 0,
                'discount_val' => 0,
                'invoice_template_id' => InvoiceTemplate::first()->id,
                'invoice_date' => now()->subDays(2)->format('Y-m-d'),
                'due_date' => now()->addDays(20)->format('Y-m-d'),
                'pbx_packprice' => 3545,
                'pbx_total_aditional_charges' => 300,
                'inv_avalara_bool' => 1,
            ]);

        $item = Item::where('name', '=', 'Enhanced Feature')->first();

        InvoiceItem::create([
            'name' => $item->name,
            'description' => $item->description,
            'discount_type' => 'fixed',
            'price' => 501,
            'quantity' => 5,
            'discount_val' => 0,
            'tax' => 0,
            'total' => 2505,
            'invoice_id' => $invoice->id,
            'item_id' => $item->id,
            'company_id' => $invoice->company_id,
        ]);

        $item = Item::where('name', '=', 'Equipment Rental')->first();

        InvoiceItem::create([
            'name' => $item->name,
            'description' => $item->description,
            'discount_type' => 'fixed',
            'price' => 501,
            'quantity' => 5,
            'discount_val' => 0,
            'tax' => 0,
            'total' => 2505,
            'invoice_id' => $invoice->id,
            'item_id' => $item->id,
            'company_id' => $invoice->company_id,
        ]);

        $dids = PbxDID::with('profile:id,name,did_rate,did_number')->get(['id', 'number', 'server', 'trunk']);
        $insert = [];
        foreach ($dids as $key => $did) {
            $insert[] = [
                'invoice_id' => $invoice->id,
                'pbx_did_id' => $did->id,
                'company_id' => $invoice->company_id,
                'creator_id' => $invoice->creator_id,
                'pbx_did_number' => $did->number,
                'pbx_did_server' => $did->server,
                'pbx_did_trunk' => $did->trunk,
                'template_did_id' => $did->template->id ?? null,
                'template_did_name' => $did->template->name ?? null,
                'template_did_rate' => $did->template->did_rate ?? null,

            ];
        }

        InvoiceDid::insert($insert);

        $extensions = PbxExtensions::whereHas('profile')->with('profile:id,name,rate,extensions_number')->get(['id', 'ext', 'name']);
        $insert = [];
        foreach ($extensions as $key => $ext) {
            $insert[] = [
                'invoice_id' => $invoice->id,
                'company_id' => $invoice->company_id,
                'creator_id' => $invoice->creator_id,
                'pbx_extension_id' => $ext->id,
                'template_extension_id' => $ext->template->id ?? null,
                'pbx_extension_name' => $ext->name,
                'pbx_extension_ext' => $ext->ext,
                'template_extension_name' => $ext->template->name ?? null,
                'template_extension_rate' => $ext->template->did_rate ?? null,

            ];
        }

        InvoiceExtension::insert($insert);

        CallDetailRegisterTotal::where('pbx_services_id', 1)->update(['invoice_id' => $invoice->id]);
    }
}
