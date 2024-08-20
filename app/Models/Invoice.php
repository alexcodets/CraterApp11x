<?php

namespace Crater\Models;

use Barryvdh\DomPDF\Facade\Pdf;
use Cache;
use Carbon\Carbon;
use Crater\Helpers\General;
use Crater\Mail\SendInvoiceMail;
use Crater\Traits\GeneratesPdfTrait;
use Crater\Traits\HasCustomFieldsTrait;
use Crater\Traits\ModelPagination;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Log;
use Mail;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Throwable;

// use Crater\Traits\SendEmailsTrait;

class Invoice extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use GeneratesPdfTrait;
    use SoftDeletes;
    use HasCustomFieldsTrait;
    use ModelPagination;

    // use SendEmailsTrait;

    public const STATUS_DRAFT = 'DRAFT';
    public const STATUS_SAVE_DRAFT = 'SAVE_DRAFT';
    public const STATUS_SENT = 'SENT';
    public const STATUS_VIEWED = 'VIEWED';
    public const STATUS_OVERDUE = 'OVERDUE';
    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_DUE = 'DUE';
    public const STATUS_UNPAID = 'UNPAID';
    public const STATUS_PARTIALLY_PAID = 'PARTIALLY_PAID';
    public const STATUS_PAID = 'PAID';

    public const DELETE_AT = null;

    protected $guarded = [
        'id',
    ];

    protected $appends = [
        'formattedInvoiceDate',
        'formattedCreatedAt',
        'formattedDueDate',
        'formattedPrevDate',
        'formattedRenewalDate',
        'formattedEndPeriodServices',
        'formattedFullPeriodService',
        'invoicePdfUrl',
        'is_recuperable',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'edited_at' => 'datetime',
            'total' => 'integer',
            'tax' => 'integer',
            'sub_total' => 'integer',
            'discount' => 'float',
            'discount_val' => 'integer',
            'invoice_date' => 'date',
        ];
    }

    public static function getNextInvoiceNumber($prefix): string
    {
        // Obtener el último número de factura en la secuencia especificada
        $lastNumber = Invoice::where('invoice_number', 'LIKE', "$prefix-%")
            ->orderByRaw("CAST(SUBSTRING_INDEX(invoice_number, '-', -1) AS UNSIGNED) DESC")
            ->value('invoice_number');

        // \Log::debug($lastNumber);
        // Obtener el número siguiente único
        $nextNumber = $lastNumber ? intval(substr($lastNumber, strlen($prefix) + 1)) + 1 : 1;
        $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Verificar la unicidad del número generado
        $existingInvoice = Invoice::where('invoice_number', "$prefix-$formattedNumber")->exists();

        // Si el número generado ya existe, buscar el siguiente número único
        while ($existingInvoice) {
            $nextNumber++;
            $formattedNumber = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            $existingInvoice = Invoice::where('invoice_number', "$prefix-$formattedNumber")->exists();
        }

        return $formattedNumber;
    }

    /**
     * @throws Throwable
     */
    public static function createInvoice($request)
    {
        $data = $request->except('items', 'taxes', 'invoice_ext', 'invoice_did', 'invoice_additional', 'get_call_detail_register_total', 'pbx_packages', 'count_extension', 'pbx_service_detail');
        $data['creator_id'] = Auth::id();

        $data['status'] = $request['save_as_draft'] == 0 ? Invoice::STATUS_DRAFT : Invoice::STATUS_SAVE_DRAFT;

        $data['company_id'] = $request->header('company');
        $data['paid_status'] = Invoice::STATUS_UNPAID;
        $data['tax_per_item'] = CompanySetting::getSetting('tax_per_item', $request->header('company')) ?? 'NO ';
        $data['retention'] = CompanySetting::getSetting('retention_active', $request->header('company')) ?? 'NO ';
        $data['discount_per_item'] = CompanySetting::getSetting('discount_per_item', $request->header('company')) ?? 'NO';
        $data['due_amount'] = $request->total;
        $data['invoice_pbx_modify'] = $request->invoice_pbx_modify;
        // avalara_bool
        $data['inv_avalara_bool'] = $request->avalara_bool;

        if ($request->has('invoiceSend')) {
            $data['status'] = Invoice::STATUS_SENT;
        }

        /// valida la cuenta de exnteiones
        if ($request->has('count_extension')) {
            if ($request->count_extension != null) {
                $data['count_extension'] = $request->count_extension;
            }
        }
        /// valida la cuenta de did
        if ($request->has('count_did')) {
            if ($request->count_did != null) {
                $data['count_did'] = $request->count_did;
            }
        }

        if ($request->has('totalAppRate')) {
            if ($request->totalAppRate != null) {
                $data['pbx_total_apprate'] = $request->totalAppRate;
            }
        }

        if ($request->has('pbx_service_price')) {
            if ($request->pbx_service_price != null) {
                $data['pbx_packprice'] = $request->pbx_service_price * 100;
            }
        }

        if ($request->has('customer_packages_id')) {
            if ($request->customer_packages_id != null) {
                $servicen = CustomerPackage::where('id', $request->customer_packages_id)->first();
                if ($servicen != null) {
                    $data['addresses_id'] = $servicen->addresses_id;
                }
            }
        }

        $hash = General::generateUniqueId();
        $data['unique_hash'] = $hash;

        if ($request->has('is_invoice_pos')) {
            if ($request->is_invoice_pos) {
                $data['status'] = 'SENT';
                $invoiceNumber = $request->invoice_number;
                $existsInvoiceNumber = DB::table('invoices')->where('is_invoice_pos', 1)->where('invoice_number', $invoiceNumber)->exists();
                while ($existsInvoiceNumber) {
                    $numberTemp = explode('-', $invoiceNumber);
                    $number = intval($numberTemp[1]);
                    $newInvoiceNumber = $numberTemp[0].'-000'.($number + 1);
                    $existsInvoiceNumber = DB::table('invoices')->where('is_invoice_pos', 1)->where('invoice_number', $newInvoiceNumber)->exists();
                    $data['invoice_number'] = $newInvoiceNumber;
                    $invoiceNumber = $newInvoiceNumber;
                }

            }
        }

        try {
            $invoice = Invoice::create($data);

        } catch (Exception $e) {
            if ($e->getCode() == 1062) {
                $invoice = Invoice::make($data);
            } else {
                // Only logs when an error other than duplicate happens
                Log::error($e->getMessage());

                throw $e;
            }
        }

        if ($request->has('contact')) {
            if (count($request->contact) != 0) {
                DB::table('contact_invoice')->insert([
                    'name' => array_key_exists('name', $request->contact) ? $request->contact['name'] : '',
                    'last_name' => array_key_exists('last_name', $request->contact) ? $request->contact['last_name'] : '',
                    'identification' => array_key_exists('identification', $request->contact) ? $request->contact['identification'] : '',
                    'phone' => array_key_exists('phone', $request->contact) ? $request->contact['phone'] : '',
                    'second_phone' => array_key_exists('second_phone', $request->contact) ? $request->contact['second_phone'] : '',
                    'email' => array_key_exists('email', $request->contact) ? $request->contact['email'] : '',
                    'invoice_id' => $invoice->id,
                    'created_at' => Carbon::now()->format('Y-m-d'),
                ]);
            }
        }

        $invoice->unique_hash = General::generateUniqueId();
        $invoice->save();

        // save tips if invoice is POS
        if ($request->has('is_invoice_pos')) {
            if ($request['tip_val'] != 0) {
                DB::table('pos_tip')->insert([
                    'tip_val' => $request['tip_val'],
                    'tip' => $request['tip'],
                    'tip_type' => $request['tip_type'],
                    'invoice_id' => $invoice->id,
                    'created_at' => Carbon::now(),
                ]);
            }

            if (isset($data['store_id'])) {
                $store = Store::where('id', $data['store_id'])->first();
                DB::table('pos_stores')->insert([
                    'name' => $store->name,
                    'description' => $store->description,
                    'created_at' => Carbon::now(),
                    'invoice_id' => $invoice->id,
                ]);
            }

        }

        /*while (Invoice::where('unique_hash', $hash)->count() > 0) {
        $invoice->unique_hash = General::generateUniqueId();
        $invoice->save();
        }*/

        if ($invoice->pbx_total_aditional_charges > 0) {
            // $invoice->pbx_total_aditional_charges = $invoice->pbx_total_aditional_charges *100;
        }

        // Tables pbx_details (invoice_pbx_did_detail and invoice_pbx_extension_detail)
        if (isset($request->pbx_service_id)) {
            // invoice_pbx_did_detail
            if (count($request->pbx_did_detail) > 0) {
                self::createInvoicePbxDidDetail($request->pbx_did_detail, $invoice->id);
            }
            // invoice_pbx_extension_detail
            if (count($request->pbx_extension_detail) > 0) {
                self::createInvoicePbxExtensionDetail($request->pbx_extension_detail, $invoice->id);
            }
        }
        //

        if (isset($request->pbx_service_id)) {
            self::createPbxExtension($invoice, $request);
            self::createPbxDid($invoice, $request);
            //   self::createPbxAdditional($invoice, $request);
            self::createPbxAdditional($invoice, $request);
            self::updateCallDetailRegisterTotals($invoice, $request);
            if ($request->invoice_pbx_modify) {
                $val = self::createPbxServicesDetail($invoice, $request);
                if ($val != 0) {
                    $invoice->count_did = $val;
                    $invoice->save();
                }
                // self::updetaPbxProfileExtensionPrice($request);
                //self::updetaPbxPackagesPrice($request);
                // self::updetaAditionalCharges($request, $invoice);
            }

            if (isset($request->addicional_charges_extension)) {
                self::createPbxAdditionalupdate($invoice->id, $request);
            }
        }
        if (isset($request->banType)) {
            self::createItems($invoice, $request);
        } else {
            if (isset($request->items)) {
                self::createItems($invoice, $request);
            }
        }

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($invoice, $request);
        }

        if (isset($request->customFields)) {
            $invoice->addCustomFields($request->customFields);
        }

        if (isset($request->pbxServiceAllowCustomapp)) {
            self::addCustomapp($invoice, $request->pbxServicesAppRate);
        }

        if ($request->has('cash_register_id')) {
            try {
                DB::table('cash_register_invoice')->insert([
                    'invoice_id' => $invoice->id,
                    'cash_register_id' => $request['cash_register_id'],
                    'created_at' => Carbon::now()->format('Y-m-d'),
                ]);
            } catch (Throwable $th) {
                Log::debug($th);
            }
        }

        if ($request->has('tables_selected')) {
            if (! empty($request['tables_selected'])) {
                foreach ($request['tables_selected'] as $tablesSelected) {
                    try {
                        DB::table('invoices_tables')->insert([
                            'invoice_id' => $invoice->id,
                            'name' => $tablesSelected['name'],
                            'quantity_persons' => $tablesSelected['quantity'],
                            'created_at' => Carbon::now(),
                        ]);

                    } catch (Throwable $th) {
                        Log::debug($th);
                    }
                }
            }
        }

        $invoice = Invoice::with([
            'items',
            'user',
            'invoiceTemplate',
            'taxes',
        ])
            ->find($invoice->id);

        return $invoice;
    }

    public static function createInvoicePbxDidDetail($dids, $invoice_id, $mode = 'create')
    {
        if ($mode === 'update') {
            DB::table('invoice_pbx_did_detail')->where('invoice_id', $invoice_id)->delete();
        }

        foreach ($dids as $did) {
            DB::table('invoice_pbx_did_detail')->insert(
                [
                    'invoice_id' => $invoice_id,
                    'name' => $did[4],
                    'quantity' => $did[1],
                    'price' => $did[2],
                    'total' => $did[3],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }

    public static function createInvoicePbxExtensionDetail($exts, $invoice_id, $mode = 'create')
    {
        if ($mode === 'update') {
            DB::table('invoice_pbx_extension_detail')->where('invoice_id', $invoice_id)->delete();
        }

        foreach ($exts as $ext) {
            DB::table('invoice_pbx_extension_detail')->insert(
                [
                    'invoice_id' => $invoice_id,
                    'name' => $ext[4],
                    'quantity' => $ext[1],
                    'price' => $ext[2],
                    'total' => $ext[3],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }

    public static function createPbxExtension($invoice, $request)
    {
        if ($request->invoice_ext) {
            foreach ($request->invoice_ext as $tax) {
                if (isset($tax['invoice_prorate'])) {
                    if (! $tax['invoice_prorate']) {
                        $tax['company_id'] = $request->header('company');
                        $tax['creator_id'] = Auth::id();
                        $tax['invoice_id'] = $invoice->id;
                        $invoice->pbxExtension()->create($tax);
                    }
                } else {
                    $tax['company_id'] = $request->header('company');
                    $tax['creator_id'] = Auth::id();
                    $tax['invoice_id'] = $invoice->id;
                    $invoice->pbxExtension()->create($tax);
                }
            }
        }
    }

    public function pbxExtension(): HasMany
    {
        return $this->hasMany(InvoiceExtension::class);
    }

    public static function createPbxDid($invoice, $request)
    {
        if ($request->invoice_did) {
            foreach ($request->invoice_did as $tax) {
                $name_prefix = $tax['template_did_name'];
                $pbxsdid = PbxServicesDID::where('pbx_service_id', $invoice->pbx_service_id)->where('pbx_did_id', $tax['pbx_did_id'])->where('custom_did_id', $tax['custom_did_id'])->first();

                if ($pbxsdid != null) {
                    $name_prefix = $pbxsdid->name_prefix;
                }

                if (isset($tax['invoice_prorate'])) {
                    if (! $tax['invoice_prorate']) {
                        $tax['company_id'] = $request->header('company');
                        $tax['creator_id'] = Auth::id();
                        $tax['invoice_id'] = $invoice->id;
                        $tax['name_prefix'] = $name_prefix;

                        if ($invoice->custom_did_id != null && $invoice->custom_did_id != 0) {
                            $tax['template_did_name'] = 'Custom DID';
                            $tax['name_prefix'] = $name_prefix;
                        }

                        foreach ($request->pbx_did_detail as $group) {
                            if (in_array($tax['pbx_did_id'], $group[5], true)) {
                                $tax['price'] = $group[2];

                                break;
                            }
                        }

                        $invoice->pbxDid()->create($tax);
                    }
                } else {
                    $tax['company_id'] = $request->header('company');
                    $tax['creator_id'] = Auth::id();
                    $tax['invoice_id'] = $invoice->id;
                    $tax['name_prefix'] = $name_prefix;

                    if ($invoice->custom_did_id != null && $invoice->custom_did_id != 0) {
                        $tax['template_did_name'] = 'Custom DID';
                        $tax['name_prefix'] = $name_prefix;
                    }

                    $tax['name_prefix'] = $name_prefix;

                    foreach ($request->pbx_did_detail as $group) {
                        if (in_array($tax['pbx_did_id'], $group[5], true)) {
                            $tax['price'] = $group[2];

                            break;
                        }
                    }
                    $invoice->pbxDid()->create($tax);
                }
            }
        }

    }

    public function pbxDid(): HasMany
    {
        return $this->hasMany(InvoiceDid::class);
    }

    public static function createPbxAdditional($invoice, $request)
    {
        // \Log::debug("Entro a 1");
        if ($request->invoice_additional) {
            // \Log::debug("Entro a 2");
            foreach ($request->invoice_additional as $tax) {
                $tax['company_id'] = $request->header('company');
                $tax['creator_id'] = $invoice->creator_id;
                $tax['invoice_id'] = $invoice->id;
                //\Log::debug($tax);
                $invoice->pbxAdditionalCharge()->create($tax);
            }
        }
    }

    public function pbxAdditionalCharge(): HasMany
    {
        return $this->hasMany(InvoiceAdditionalCharge::class);
    }

    public static function updateCallDetailRegisterTotals($invoice, $request)
    {
        $service = PbxServices::findOrFail($request->pbx_service_id);
        $data['invoice_id'] = $invoice->id;
        $service->getCallDetailRegisterTotal()->update($data);

        /// para actualizar
        $cdr_total2 = DB::table('call_detail_register_totals')->where('pbx_services_id', '=', $request->pbx_service_id)->where('invoice_id', '=', $invoice->id)->get();

        $call = CallHistoryIndi::WhereIn('call_detail_register_totals_id', $cdr_total2->pluck('id')->toArray())->get();

        $totalmonto = 0;
        $totaltax = 0;
        foreach ($call as $charges) {
            $totalmonto = $totalmonto + $charges->amout;
            $totaltax = $totaltax + $charges->taxamount;
            $charges->invoice_id = $invoice->id;
            $charges->save();
        }

        $invoice->prepaid_amount = $totalmonto;
        $invoice->tax_prepaid_amount = $totaltax;
        $invoice->save();

    }

    public static function createPbxServicesDetail($invoice, $request)
    {
        $cont = 0;
        if ($request->pbx_service_detail) {
            foreach ($request->pbx_service_detail as $tax) {
                $nuevo = new PbxServicesDetail();
                $nuevo['count_did'] = $tax[1];
                $nuevo['price_did'] = $tax[2];
                $nuevo['name'] = $tax[4];
                $nuevo['invoice_id'] = $invoice->id;
                $nuevo['count_extension'] = $request->count_extension;
                $nuevo->save();
                $cont = $nuevo['count_did'] + $cont;
            }
        }

        return $cont;
    }

    public static function createPbxAdditionalupdate($invoice_id, $request)
    {
        try {
            $invoice = Invoice::where('id', $invoice_id)->first();
            if ($request->addicional_charges_extension) {
                foreach ($request->addicional_charges_extension as $tax) {

                    $invaddi = InvoiceAdditionalCharge::where('invoice_id', $invoice->id)->where('additional_charge_id', $tax['id'])->first();
                    if ($invaddi != null) {

                        $invaddi->additional_charge_amount = $tax['additional_charge_amount'];
                        if ($tax['profile_extension_id'] != null) {
                            $invaddi->qty = $invoice->count_extension;
                            $invaddi->profile_extension_id = $tax['profile_extension_id'];
                        }

                        if ($tax['profile_did_id'] != null) {
                            $invaddi->qty = $invoice->count_did;
                            $invaddi->profile_did_id = $tax['profile_did_id'];
                        }

                        $invaddi->total = $invaddi->additional_charge_amount * $invaddi->qty;

                        $invaddi->save();
                    }

                }
            }
        } catch (Throwable $th) {
            Log::debug($th);
        }

    }

    public static function createItems($invoice, $request, $operation = 'creation')
    {
        $invoiceItems = $request->items;
        $invoiceItemss = [];
        //  Log::debug('Inside CreateItems');
        // Log::debug('invoice model', ['invoice' => $invoice]);
        // Log::debug('invoice model', ['invoice' => $invoiceItems]);

        //Log::debug("Lineas items");
        foreach ($invoiceItems as $invoiceItem) {

            $invoiceItem['company_id'] = $request->header('company');

            if (array_key_exists('retentions', $invoiceItem)) {
                if ($invoice->retention == 'YES') {
                    $invoiceItem['retention_id'] = null;
                    $invoiceItem['retention_concept'] = null;
                    $invoiceItem['retention_percentage'] = null;
                    $invoiceItem['retention_amount'] = 0;
                    if ($invoiceItem['retentions'] != null) {
                        if (isset($invoiceItem['retentions']['id'])) {
                            $invoiceItem['retention_id'] = $invoiceItem['retentions']['id'];
                            $invoiceItem['retention_concept'] = $invoiceItem['retentions']['concept'];
                            $invoiceItem['retention_percentage'] = $invoiceItem['retentions']['percentage'];
                            $invoiceItem['retention_amount'] = $invoiceItem['retentions']['retention_amount'];
                        }
                    }
                }
            }

            if (! array_key_exists('item_id', $invoiceItem)) {

                if (array_key_exists('items_id', $invoiceItem)) {
                    $invoiceItem['item_id'] = $invoiceItem['items_id'];
                }

            }

            $itemtest = Item::where('id', $invoiceItem['item_id'])->first();

            if ($itemtest == null) {
                $invoiceItem['item_id'] = null;
            }
            $item = $invoice->items()->create($invoiceItem);
            $invoiceItemss[] = $item;

            if (array_key_exists('taxes', $invoiceItem) && $invoiceItem['taxes']) {
                self::createItemsTaxes($invoiceItem, $request, $item);
            }
        }

        //if ($request->avalara_bool === true) {

        if ($invoice->inv_avalara_bool && $operation === 'update') {

            //   Log::debug('Is Avalara Bool');
            //self::createItemsAvalaraTaxes($invoiceItemss, $request, $invoice);
            //$service = new AvalaraAdjustmentService();
        }
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public static function createItemsTaxes($invoiceItem, $request, $item)
    {
        foreach ($invoiceItem['taxes'] as $tax) {
            $tax['company_id'] = $request->header('company');
            if (gettype($tax['amount']) !== 'NULL') {
                $item->taxes()->create($tax);
            }
        }
    }

    public function taxes(): HasMany
    {
        return $this->hasMany(Tax::class);
    }

    public static function createTaxes($invoice, $request)
    {
        if ($request->has('taxes') && (! empty($request->taxes))) {
            foreach ($request->taxes as $tax) {
                $tax['company_id'] = $request->header('company');

                if (gettype($tax['amount']) !== 'NULL') {
                    $invoice->taxes()->create($tax);
                }
            }
        }
    }

    public static function addCustomapp($invoice, $pbxServicesAppRate)
    {
        if (isset($pbxServicesAppRate)) {
            $invoice->pbxAppRates()->delete();
            foreach ($pbxServicesAppRate as $value) {
                InvoiceAppRates::create([
                    'app_name' => $value['app_name'],
                    'quantity' => $value['quantity'],
                    'costo' => $value['costo'] / 100,
                    'pbx_package_id' => $value['pbx_package_id'],
                    'pbx_service_id' => $invoice['pbx_service_id'],
                    'invoice_id' => $invoice->id,
                ]);
            }
        }
    }

    public function pbxAppRates(): HasMany
    {
        return $this->hasMany(InvoiceAppRates::class);
    }

    public static function createInvoiceProrate($request)
    {
        $data = $request->except('items', 'taxes', 'invoice_ext', 'invoice_did', 'invoice_additional', 'get_call_detail_register_total', 'pbx_packages', 'count_extension', 'pbx_service_detail');
        $data['creator_id'] = Auth::id();
        $data['status'] = Invoice::STATUS_DRAFT;
        // $data['company_id'] = $request->company_id;
        $data['paid_status'] = Invoice::STATUS_UNPAID;
        // $data['due_amount'] = $request->total;
        // $data['invoice_pbx_modify'] = $request->invoice_pbx_modify;

        $invoice = Invoice::create($data->toArray());
        $invoice->unique_hash = General::generateUniqueId();
        $invoice->invoiceprorate = 1;
        $invoice->pbxservice_date_prev = $invoice->created_at;
        if ($invoice->pbx_total_aditional_charges > 0) {
            $invoice->pbx_total_aditional_charges = $invoice->pbx_total_aditional_charges / 100;
        }

        $invoice->sub_total = $data['subtotalpro'];

        $invoice->save();

        while (Invoice::where('unique_hash', $invoice->unique_hash)->count() > 0) {
            $invoice->unique_hash = General::generateUniqueId();
            $invoice->save();
        }

        if (isset($request->pbx_service_id)) {
            self::createPbxExtension($invoice, $request);
            self::createPbxDid($invoice, $request);
            self::createPbxAdditional($invoice, $request);
            self::updateCallDetailRegisterTotals($invoice, $request);
            if ($request->invoice_pbx_modify) {
                $val = self::createPbxServicesDetail($invoice, $request);
                if ($val != 0) {
                    $invoice->count_did = $val;
                    $invoice->save();
                }
                //self::updetaPbxProfileExtensionPrice($request);
                //self::updetaPbxPackagesPrice($request);
                //self::updetaAditionalCharges($request, $invoice);
            }

            if (isset($request->addicional_charges_extension)) {
                self::createPbxAdditionalupdate($invoice->id, $request);
            }

        }
        if (isset($request->banType)) {
            self::createItems($invoice, $request);
        } else {
            if (isset($request->items)) {
                self::createItems($invoice, $request);
            }
        }

        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($invoice, $request);
        }

        if (isset($request->customFields)) {
            $invoice->addCustomFields($request->customFields);
        }

        $invoice = Invoice::with([
            'items',
            'user',
            'invoiceTemplate',
            'taxes',
        ])
            ->find($invoice->id);

        return $invoice;
    }

    public static function updetaPbxPackagesPrice($request)
    {
        $PbxPackages = PbxPackages::find($request->pbx_packages['id']);
        $PbxPackages->rate = $request->pbx_packages['rate'];
        $PbxPackages->update();
    }

    public static function updetaPbxProfileExtensionPrice($request)
    {
        if ($request->pbx_packages['profile_extensions']) {
            $PbxPackages = ProfileExtensions::find($request->pbx_packages['profile_extensions']['id']);
            $PbxPackages->rate = $request->pbx_packages['profile_extensions']['rate'];
            $PbxPackages->update();
        }
    }

    public static function updetaAditionalCharges($request, $invoice_id)
    {

        if ($request->pbx_packages['profile_extensions'] != null) {
            if ($request->pbx_packages['profile_extensions']['aditional_charges_a'] != null) {

                foreach ($request->pbx_packages['profile_extensions']['aditional_charges_a'] as $tax) {
                    //   \Log::debug($tax);
                    $Aditional = AditionalCharges::find($tax['id']);
                    $Aditional->amount = $tax['amount'];
                    $Aditional->update();
                }
            }
        }
        if ($request->pbx_packages['profile_did'] != null) {
            if ($request->pbx_packages['profile_did']['aditional_charges_a'] != null) {
                foreach ($request->pbx_packages['profile_did']['aditional_charges_a'] as $tax) {
                    //  \Log::debug($tax);
                    $Aditional = AditionalCharges::find($tax['id']);
                    $Aditional->amount = $tax['amount'];
                    $Aditional->update();
                }
            }
        }
    }

    public static function updetaPbxServicesDetail($invoice, $request)
    {
        $cont = 0;
        // \Log::debug("entro");
        foreach ($request->pbx_service_detail as $tax) {
            //  \Log::debug($tax);
            // $Modif = PbxServicesDetail::find($tax[5]);
            $Modif = new PbxServicesDetail();
            $Modif->invoice_id = $request->id;
            if (isset($tax[5])) {
                $Modif = PbxServicesDetail::find($tax[5]);
            }

            $Modif->count = $tax[0];
            $Modif->count_did = $tax[1];
            $Modif->price_did = $tax[2];
            $Modif->name = $tax[4];
            $Modif->count_extension = $request->count_extension;
            $Modif->save();
            $cont = $Modif->count_did + $cont;
        }

        return $cont;
    }

    public function setInvoiceDateAttribute($value)
    {
        if ($value) {
            $this->attributes['invoice_date'] = Carbon::parse($value)->format('Y-m-d');
            //Carbon::createFromFormat('Y-m-d', $value);
        }
    }

    public function setDueDateAttribute($value)
    {
        if ($value) {
            $this->attributes['due_date'] = Carbon::parse($value)->format('Y-m-d');
        }
    }

    public function setNewInvoiceNumber()
    {
        $prefix = $this->company->settings()->where('option', 'invoice_prefix')
            ->first('value');
        $lastOrder = $this->company->invoices()
            ->where('invoice_number', 'LIKE', $prefix->value.'-%')
            ->withTrashed()
            ->orderBy('invoice_number', 'desc')
            ->first('invoice_number')->invoice_number ?? null;

        $this->invoice_number = $prefix->value.'-'.sprintf('%06d', intval($lastOrder ? explode('-', $lastOrder)[1] : 0) + 1);
        $this->save();

    }

    public function updateInvoiceNumberAndAvalaraCode()
    {
        $this->timestamps = false;
        $this->edited_at = $this->updated_at;
        //$this->setNewInvoiceNumber();
        $this->save();
        $this->timestamps = true;

    }

    public function emailLogs(): MorphMany
    {
        return $this->morphMany(\Crater\Models\EmailLog::class, 'mailable');
    }

    public function addresses(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Packages::class, 'invoice_customer_packages', 'invoice_id', 'package_id')
            ->withPivot('id', 'status');
    }

    public function noTaxableItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)
            ->whereHas('item', function ($query) {
                $query->where('no_taxable', 1);
            });

    }

    public function taxableItems(): HasMany
    {
        return $this->hasMany(InvoiceItem::class)
            ->whereHas('item', function ($query) {
                $query->where('no_taxable', 0);
            });

    }

    public function serviceDetails(): HasMany
    {
        return $this->hasMany(PbxServicesDetail::class);
    }

    public function invoicePbxExtensionDetail(): HasMany
    {
        return $this->hasMany(InvoicePbxExtensionDetail::class);
    }

    public function invoicePbxDidDetail(): HasMany
    {
        return $this->hasMany(InvoicePbxDidDetail::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function invoiceTemplate(): BelongsTo
    {
        return $this->belongsTo(InvoiceTemplate::class);
    }

    public function avalaraInvoice(): HasOne
    {
        return $this->avalaraInvoiceCurrent();
    }

    public function avalaraInvoiceCurrent(): HasOne
    {
        return $this->hasOne(AvalaraInvoice::class)->where('status', '=', AvalaraInvoice::STATUS_ACTIVE);
    }

    public function avalaraLog(): HasOne
    {
        return $this->hasOne(AvalaraLog::class)->whereNull('avalara_log_id')->latest();
    }

    public function avalaraLogs(): HasMany
    {
        return $this->hasMany(AvalaraLog::class);

    }

    public function InvoiceAdditionalCharges(): HasMany
    {
        return $this->hasMany(InvoiceAdditionalCharge::class);
    }

    public function InvoiceLateFee(): HasMany
    {
        return $this->hasMany(CompanySetting::class, 'company_id', 'company_id');
    }

    public function lateFee(): HasMany
    {
        return $this->hasMany(CompanySetting::class, 'company_id', 'company_id');
    }

    public function lateFeeHour(): HasMany
    {
        return $this->hasMany(CompanySetting::class, 'company_id', 'company_id')->where('option', 'late_fee_hour');
    }

    public function getAvalaraDocumentCodeAttribute(): string
    {
        return str_pad($this->id, 5, '0', STR_PAD_LEFT).'.'.($this->edited_at ? $this->edited_at->unix() : $this->created_at->unix());
    }

    public function getInvoicePdfUrlAttribute()
    {
        return url('/invoices/pdf/'.$this->unique_hash);
    }

    public function getIsRecuperableAttribute()
    {
        $is_recuperable = 0;
        if ($this->deleted_at) {
            // fecha actual
            $now = Carbon::now();
            // fecha de eliminacion (deleted_at) + 30 dias
            $formatted_deleted_at = $this->deleted_at->addDay(30);
            // SI fecha de (eliminacion + 30 dias) > fecha actual. Todavia se puede recuperar el invoice
            if ($formatted_deleted_at->gte($now)) {
                $is_recuperable = 1;
            }
        }

        return $is_recuperable;
    }

    public function getInvoiceNumAttribute()
    {
        $position = $this->strposX($this->invoice_number, '-', 1) + 1;

        return substr($this->invoice_number, $position);
    }

    private function strposX($haystack, $needle, $number)
    {
        if ($number == '1') {
            return strpos($haystack, $needle);
        } elseif ($number > '1') {
            return strpos(
                $haystack,
                $needle,
                $this->strposX($haystack, $needle, $number - 1) + strlen($needle)
            );
        } else {
            return error_log('Error: Value for parameter $number is out of range');
        }
    }

    public function getAvalaraInvoiceNumberAttribute(): string
    {
        return $this->invoice_number.'-A'.str_pad($this->avalaraInvoices()->count() + 1, 2, '0', STR_PAD_LEFT);
    }

    public function avalaraInvoices(): HasMany
    {
        return $this->hasMany(AvalaraInvoice::class);
    }

    public function getInvoicePrefixAttribute()
    {
        $prefix = explode('-', $this->invoice_number)[0];

        return $prefix;
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        if (is_null($this->created_at)) {
            return null;
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format($dateFormat);
    }

    public function getFormattedDueDateAttribute($value)
    {
        if (is_null($this->due_date)) {
            return null;
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->due_date)->format($dateFormat);
    }

    public function getFormattedInvoiceDateAttribute($value)
    {
        if (is_null($this->invoice_date)) {
            return null;
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->invoice_date)->format($dateFormat);
    }

    public function getFormattedPrevDateAttribute($value)
    {
        if (is_null($this->pbxservice_date_prev)) {
            return "";
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->pbxservice_date_prev)->format($dateFormat);
    }

    public function getFormattedRenewalDateAttribute($value)
    {
        if (is_null($this->pbxservice_date_renewal)) {
            return "";
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->pbxservice_date_renewal)->format($dateFormat);
    }

    public function getFormattedEndPeriodServicesAttribute($value)
    {
        if (is_null($this->end_period_services)) {
            return null;
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->end_period_services)->format($dateFormat);
    }

    public function getFormattedFullPeriodServiceAttribute($value)
    {

        // Verificar si todos los campos están vacíos o son null
        if (empty($this->pbxservice_date_renewal) && empty($this->end_period_services) && empty($this->pbxservice_date_prev)) {
            return "";
        }

        // Obtener el formato de fecha de la configuración de la compañía
        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        // Verificar si 'pbxservice_date_renewal' y 'end_period_services' no están vacíos o son null
        if (! empty($this->pbxservice_date_renewal) && ! empty($this->end_period_services)) {
            // Formatear y retornar la cadena concatenada
            return Carbon::parse($this->pbxservice_date_renewal)->format($dateFormat)." - ".Carbon::parse($this->end_period_services)->format($dateFormat);
        }

        // Verificar si 'end_period_services' está vacío o es null y 'pbxservice_date_renewal' no lo está
        if (empty($this->end_period_services) && ! empty($this->pbxservice_date_renewal)) {
            // Formatear y retornar la cadena concatenada con 'pbxservice_date_prev'
            return Carbon::parse($this->pbxservice_date_prev)->format($dateFormat)." - ".Carbon::parse($this->pbxservice_date_renewal)->format($dateFormat);
        }

        // Si ninguna de las condiciones anteriores se cumple, retornar vacío
        return "";

    }

    public function scopeWhereStatus($query, $status)
    {
        return $query->where('invoices.status', $status);
    }

    // addCustomapp

    public function scopeWherePaidStatus($query, $status)
    {
        return $query->where('invoices.paid_status', $status);
    }

    public function scopeWhereDueStatus($query, $status)
    {
        return $query->whereIn('invoices.paid_status', [
            self::STATUS_UNPAID,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    public function scopeDueInvoiceStatus($query)
    {
        return $query->whereIn('invoices.paid_status', [
            self::STATUS_UNPAID,
            self::STATUS_PARTIALLY_PAID,
        ]);
    }

    public function scopeWhereStatusOc($query, $status)
    {

        if ($status === 'OPEN' || $status === 'open') {
            return $query->where('invoices.status', self::STATUS_SENT)->orWhere('invoices.status', self::STATUS_OVERDUE);
        } elseif ($status === 'CLOSE' || $status === 'close') {
            return $query->where('invoices.status', self::STATUS_COMPLETED);
        }

        return $query;
    }

    public function scopeWhereInvoiceNumber($query, $invoiceNumber)
    {
        return $query->where('invoices.invoice_number', 'LIKE', '%'.$invoiceNumber.'%');
    }

    public function scopeWhereCustomcode($query, $customcode)
    {
        // filtrar por customcode de cliente
        $query->whereHas('user', function ($query) use ($customcode) {
            $query->where('customcode', 'LIKE', '%'.$customcode.'%');
        });
    }

    public function scopeInvoicesBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'invoices.invoice_date',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereSearch($query, $search)
    {
        foreach (explode(' ', $search) as $term) {
            $query->whereHas('user', function ($query) use ($term) {
                $query->where('name', 'LIKE', '%'.$term.'%')
                    ->orWhere('contact_name', 'LIKE', '%'.$term.'%')
                    ->orWhere('company_name', 'LIKE', '%'.$term.'%');
            });
        }
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }

    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);
        if ($filters->get('search')) {
            $query->whereSearch($filters->get('search'));
        }

        if ($filters->get('status')) {
            if (
                $filters->get('status') == self::STATUS_UNPAID ||
                $filters->get('status') == self::STATUS_PARTIALLY_PAID ||
                $filters->get('status') == self::STATUS_PAID
            ) {
                $query->wherePaidStatus($filters->get('status'));
            } elseif ($filters->get('status') == self::STATUS_DUE) {
                $query->whereDueStatus($filters->get('status'));
            } else {
                $query->whereStatus($filters->get('status'));
            }
        }

        if ($filters->get('status_oc')) {
            $query->whereStatusOc($filters->get('status_oc'));
        }

        if ($filters->get('paid_status')) {
            $query->wherePaidStatus($filters->get('status'));
        }

        if ($filters->get('invoice_id')) {
            $query->whereInvoice($filters->get('invoice_id'));
        }

        if ($filters->get('invoice_number')) {
            $query->whereInvoiceNumber($filters->get('invoice_number'));
        }

        if ($filters->get('customcode')) {
            // Log::info('customcode: ' . $filters->get('customcode'));
            $query->whereCustomcode($filters->get('customcode'));
        }

        if ($filters->get('from_date') && $filters->get('to_date')) {
            $start = Carbon::createFromFormat('Y-m-d', $filters->get('from_date'));
            $end = Carbon::createFromFormat('Y-m-d', $filters->get('to_date'));
            $query->invoicesBetween($start, $end);
        }

        if ($filters->get('customer_id')) {
            $query->whereCustomer($filters->get('customer_id'));
        }
        if ($filters->get('customer')) {

            if (preg_match('~[0-9]+~', $filters->get('customer'))) {
                $filters->put('customer', explode(',', $filters->get('customer')));
                $query->WhereCustomerArray($filters->get('customer'));
            }
        }

        if ($filters->get('country')) {
            $query->whereCountry($filters->get('country'));
        }

        if ($filters->get('state')) {
            $query->whereState($filters->get('state'));
        }

        if ($filters->get('orderByField') || $filters->get('orderBy')) {
            $field = $filters->get('orderByField') ? $filters->get('orderByField') : 'invoice_number';
            $orderBy = $filters->get('orderBy') ? $filters->get('orderBy') : 'asc';
            $query->whereOrder($field, $orderBy);
        }
    }

    public function scopeWhereInvoice($query, $invoice_id)
    {
        $query->orWhere('id', $invoice_id);
    }

    public function scopeWhereCompany($query, $company_id)
    {
        $query->where('invoices.company_id', $company_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->where('invoices.user_id', $customer_id);
    }

    public function scopewhereCountry($query, $customer_id)
    {
        $array = [];
        $array = Address::where('country_id', $customer_id)->pluck('user_id')->toarray();

        $query->whereIN('invoices.user_id', $array);
    }

    public function scopewhereState($query, $customer_id)
    {

        $array = Address::where('state_id', $customer_id)->pluck('user_id')->toarray();
        $query->whereIN('invoices.user_id', $array);
    }

    public function scopeWhereCustomerArray($query, $customer_id)
    {
        $query->whereIN('invoices.user_id', $customer_id);
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function updateInvoice($request)
    {
        $data = $request->except('items');

        //$data["pbx_total_aditional_charges"] = $data["pbx_total_aditional_charges"] ;

        $data['pbx_service_price'] = $request->pbx_service_price;
        $oldAmount = $this->total;

        if ($oldAmount != $request->total) {
            $oldAmount = (int) round($request->total) - (int) $oldAmount;
        } else {
            $oldAmount = 0;
        }

        $data['due_amount'] = ($this->due_amount + $oldAmount);

        if ($data['status'] != 'SAVE_DRAFT') {
            if ($data['due_amount'] == 0 && $this->paid_status != Invoice::STATUS_PAID) {
                $data['status'] = Invoice::STATUS_COMPLETED;
                $data['paid_status'] = Invoice::STATUS_PAID;
            } elseif ($this->due_amount < 0 && $this->paid_status != Invoice::STATUS_UNPAID) {
                return response()->json([
                    'error' => 'invalid_due_amount',
                ]);
            } elseif ($data['due_amount'] != 0 && $this->paid_status == Invoice::STATUS_PAID) {
                $data['status'] = $this->getPreviousStatus();
                $data['paid_status'] = Invoice::STATUS_PARTIALLY_PAID;
            }
        } else {
            $data['status'] = $request['save_as_draft'] == 0 ? Invoice::STATUS_DRAFT : Invoice::STATUS_SAVE_DRAFT;
        }

        /// valida la cuenta de extensiones
        if ($request->has('count_extension')) {
            if ($request->count_extension != null) {
                $data['count_extension'] = $request->count_extension;
            }
        }
        /// valida la cuenta de did
        if ($request->has('count_did')) {
            if ($request->count_did != null) {
                $data['count_did'] = $request->count_did;
            }
        }

        if ($request->has('totalAppRate')) {
            if ($request->totalAppRate != null) {
                $data['pbx_total_apprate'] = $request->totalAppRate;
            }
        }

        if ($request->has('pbx_service_price')) {
            if ($request->pbx_service_price != null) {
                $data['pbx_packprice'] = $request->pbx_service_price * 100;
            }
        }

        if ($request->pbx_service_id != null) {
            if ($request->pbx_service_price != null) {
            }
        }

        // invoice_pbx_did_detail
        if (isset($request->pbx_service_id)) {
            if (count($request->pbx_did_detail) > 0) {
                self::createInvoicePbxDidDetail($request->pbx_did_detail, $data['id'], 'update');
            }
            // invoice_pbx_extension_detail
            if (count($request->pbx_extension_detail) > 0) {
                self::createInvoicePbxExtensionDetail($request->pbx_extension_detail, $data['id'], 'update');
            }
        }

        $data['is_edited'] = $request['is_edited'];

        if ($request['is_invoice_pos']) {
            $data['status'] = 'SENT';
        }
        $this->update($data);
        if ($request->pbx_service_id != null) {

            // self::updetaPbxPackagesPrice($request);
            // self::updetaPbxProfileExtensionPrice($request);
            //self::updetaAditionalCharges($request, $this->id);

            if (isset($request->addicional_charges_extension)) {
                self::createPbxAdditionalupdate($this->id, $request);
            }
        }

        /* if($request->banType){ */
        $this->items()->delete();
        /* } */
        $this->taxes()->delete();
        /* if($request->banType){ */

        self::createItems($this, $request, 'update');
        /* } */
        if ($request->has('taxes') && (! empty($request->taxes))) {
            self::createTaxes($this, $request);
        }

        if ($request->customFields) {
            $this->updateCustomFields($request->customFields);
        }

        $invoice = Invoice::with([
            'items',
            'user',
            'invoiceTemplate',
            'taxes',
        ])
            ->find($this->id);

        if (isset($request->pbxServiceAllowCustomapp)) {
            self::addCustomapp($invoice, $request->pbxServicesAppRate);
        }
        $this->updateInvoiceAdditionalCharges($invoice, $request);

        return $invoice;
    }

    public function getPreviousStatus()
    {
        if ($this->due_date < Carbon::now()) {
            return self::STATUS_OVERDUE;
        } elseif ($this->viewed) {
            return self::STATUS_VIEWED;
        } elseif ($this->sent) {
            return self::STATUS_SENT;
        } else {
            return self::STATUS_DRAFT;
        }
    }

    public function updateInvoiceAdditionalCharges($invoice, $request)
    {
        foreach ($request['inv_addtional_char'] as $item) {
            try {

                InvoiceAdditionalCharge::updateOrCreate(
                    ['invoice_id' => $item['invoice_id'], 'id' => $item['id']],
                    [
                        'qty' => $item['qty'],
                        'additional_charge_amount' => $item['additional_charge_amount'],
                        'total' => $item['total'],
                        'additional_charge_id' => $item['additional_charge_id'],
                        'company_id' => $item['company_id'],
                        'creator_id' => $item['creator_id'],
                        'additional_charge_name' => $item['additional_charge_name'],
                        'template_name' => $item['template_name'],
                        'additional_charge_type' => $item['additional_charge_type'],
                        'updated_at' => Carbon::now()->format('Y-m-d'),
                        'profile_extension_id' => $item['profile_extension_id'],
                        'profile_did_id' => $item['profile_did_id'],

                    ]
                );
            } catch (Throwable $th) {
                Log::debug($th);
            }
        }
    }

    public function getPDFData()
    {
        $taxTypes = [];
        $taxes = [];
        $labels = [];

        if ($this->tax_per_item === 'YES') {
            // Primero, construimos un array asociativo con todos los tipos de impuestos y sus totales
            $taxTotals = [];

            foreach ($this->items as $item) {
                foreach ($item->taxes as $tax) {
                    if (! array_key_exists($tax->name, $taxTotals)) {
                        $taxTotals[$tax->name] = [
                            'total' => 0,
                            'label' => $tax->name.' ('.$tax->percent.'%)',
                        ];
                    }
                    $taxTotals[$tax->name]['total'] += $tax->amount;
                }
            }

            // Ahora, separamos los tipos de impuestos y los totales en dos arrays separados
            foreach ($taxTotals as $taxName => $taxInfo) {
                $taxTypes[] = $taxName;
                $labels[] = $taxInfo['label'];
                $taxes[] = $taxInfo['total'];
            }
        }

        $isPdfPos = $this->is_pdf_pos;
        $isInvoicePos = $this->is_invoice_pos;

        $invoiceContact = null;
        $cashRegister = null;
        $tip = null;
        $store = null;

        if ($this->is_invoice_pos) {
            $invoiceContact = DB::table('contact_invoice')->where('invoice_id', $this->id)->get()->collect();
            $cashRegister = DB::table('cash_register_invoice')
                ->join('cash_register', 'cash_register.id', '=', 'cash_register_invoice.cash_register_id')
                ->where('cash_register_invoice.invoice_id', $this->id)
                ->get()->collect();
            $tip = DB::table('pos_tip')->where('invoice_id', $this->id)->first();
            Log::debug('tip', [$tip]);
            if ($tip) {
                $this->tip = $tip->tip;
                $this->tip_val = $tip->tip_val;
                $this->tip_type = $tip->tip_type;
            }
            $store = DB::table('pos_stores')->where('invoice_id', $this->id)->first();
        }

        // Asigna a $invoiceTemplate el primer template de factura con vista 'template_pos' si $isPdfPos es verdadero,
        // de lo contrario, busca el template de factura por $this->invoice_template_id.
        $invoiceTemplate = $isPdfPos ? InvoiceTemplate::where('view', 'template_pos')->first()
        : InvoiceTemplate::find($this->invoice_template_id);

        // Si $isPdfPos es verdadero, obtiene todas las mesas asociadas con la factura actual,
        // de lo contrario, asigna un array vacío a $tablesSelected.
        $tablesSelected = $isPdfPos ? DB::table('invoices_tables')->where('invoice_id', $this->id)->get()
        : [];

        $company = Company::find($this->company_id);

        $logo = $company->logo_base_64;
        $colorInvoice = CompanySetting::getSetting('color_invoice', $this->company_id);
        $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        $solaNOmultipe = PaymentMethod::where('is_multiple', 0)->pluck('id')->toarray();
        $listpay = Payment::where('invoice_id', $this->id)
            ->whereNotNull('transaction_status')
            ->where(function ($query) use ($solaNOmultipe) {
                $query->whereIn('payment_method_id', $solaNOmultipe)
                    ->orWhereNull('payment_method_id');
            })
            ->get();

        $payarray = [];
        $cont = 0;
        $totalpay = 0;

        /// foreach para pagos normales
        foreach ($listpay as $pay) {
            $payarray[$cont]['payment_date'] = $pay->payment_date;
            $payarray[$cont]['payment_number'] = $pay->payment_number;
            $payarray[$cont]['transaction_status'] = $pay->transaction_status;
            $payarray[$cont]['responsible'] = $pay->creator_id != null ? DB::table('users')->where('id', $pay->creator_id)->get() : null;
            $payarray[$cont]['amount'] = $pay->amount;
            $payarray[$cont]['type'] = null;

            if ($pay->authorize_id !== null && $pay->aux_vault_id === null && $pay->payment_paypal_id === null) {
                // Consulta directa a la tabla 'authorize'
                $authorize = DB::table('authorize')->where('id', $pay->authorize_id)->first();

                if ($authorize != null) {
                    $payarray[$cont]['Paymentgateway'] = "Authorize";
                    if ($authorize->ACH_type == null) {
                        $payarray[$cont]['type'] = 'Credit Card';
                        $payarray[$cont]['card_number'] = $authorize->card_number;
                        $payarray[$cont]['credit_card'] = $authorize->credit_card;
                        $payarray[$cont]['transaction_id'] = $authorize->transaction_id;
                    } else {
                        $payarray[$cont]['type'] = 'ACH';
                        $payarray[$cont]['type_ach'] = $authorize->ACH_type;
                        $payarray[$cont]['account_number'] = substr($authorize->account_number, -4);
                        $payarray[$cont]['bank_name'] = $authorize->bank_name;
                        $payarray[$cont]['num_check'] = $authorize->num_check;
                        $payarray[$cont]['routing_number'] = $authorize->routing_number;
                        $payarray[$cont]['transaction_id'] = $authorize->transaction_id;

                    }
                }

            } elseif ($pay->aux_vault_id !== null && $pay->authorize_id === null && $pay->payment_paypal_id === null) {
                // Consulta directa a la tabla 'aux_vaults'
                $auxpay = DB::table('aux_vaults')->where('id', $pay->aux_vault_id)->first();

                if ($auxpay != null) {
                    $payarray[$cont]['Paymentgateway'] = "Auxpay";
                    if ($auxpay->ach_routing_number == null) {
                        $payarray[$cont]['type'] = 'Credit Card';
                        $payarray[$cont]['card_number'] = $auxpay->card_number;
                        $payarray[$cont]['credit_card'] = "";
                        $payarray[$cont]['transaction_id'] = $auxpay->transaction_id;
                    } else {
                        $payarray[$cont]['type'] = 'ACH';
                        $payarray[$cont]['type_ach'] = "";
                        $payarray[$cont]['account_number'] = substr($auxpay->ach_account_number, -4);
                        $payarray[$cont]['bank_name'] = "";
                        $payarray[$cont]['num_check'] = "";
                        $payarray[$cont]['routing_number'] = $auxpay->ach_routing_number;
                        $payarray[$cont]['transaction_id'] = $auxpay->transaction_id;

                    }
                }

            } elseif ($pay->payment_paypal_id !== null && $pay->authorize_id === null && $pay->aux_vault_id === null) {
                // Consulta directa a la tabla 'payments_paypals'
                $paypalobjet = DB::table('payments_paypals')->where('id', $pay->payment_paypal_id)->first();

                if ($paypalobjet != null) {
                    $payarray[$cont]['Paymentgateway'] = "Paypal";
                    $payarray[$cont]['type'] = 'Credit Card';
                    $payarray[$cont]['card_number'] = $paypalobjet->card_number;
                    $payarray[$cont]['credit_card'] = "";
                    $payarray[$cont]['transaction_id'] = $paypalobjet->transaction_id;

                }
            }

            if ($pay->payment_method_id == null) {
                $payarray[$cont]['payment_method'] = 'Customer Credit Balance';
            } else {
                $paymentmethod = PaymentMethod::where('id', $pay->payment_method_id)->first();
                if ($paymentmethod != null) {
                    $payarray[$cont]['payment_method'] = $paymentmethod->name;
                } else {
                    $payarray[$cont]['payment_method'] = 'N/A';
                }
            }

            if ($pay['transaction_status'] == 'Approved') {
                $totalpay = $totalpay + $payarray[$cont]['amount'];
            }

            $cont++;
        }

        $solaNOmultipe = PaymentMethod::where('is_multiple', 1)->pluck('id')->toarray();
        $listpay = Payment::where('invoice_id', $this->id)->whereNotNull('transaction_status')->whereIn('payment_method_id', $solaNOmultipe)->get();

        ///
        $received = $returned = 0;
        /// foreach para pagos nque son multiple
        foreach ($listpay as $pay) {
            $listado = DB::table('payments_payment_methods')->where('payment_id', $pay->id)->get();

            if ($listado->isNotEmpty()) {
                $received += DB::table('payments_payment_methods')
                    ->where('payment_id', $pay->id)
                    ->sum('received');

                $returned += DB::table('payments_payment_methods')
                    ->where('payment_id', $pay->id)
                    ->sum('returned');
            }

            foreach ($listado as $pay2) {
                $payarray[$cont]['payment_date'] = $pay->payment_date;
                $payarray[$cont]['payment_number'] = $pay->payment_number;
                $payarray[$cont]['transaction_status'] = $pay->transaction_status;
                $payarray[$cont]['amount'] = $pay2->amount;
                $payarray[$cont]['type'] = null;
                $paymentmethod = PaymentMethod::where('id', $pay2->payment_method_id)->first();
                $payarray[$cont]['responsible'] = $pay->creator_id != null ? DB::table('users')->where('id', $pay->creator_id)->get() : null;
                if ($paymentmethod != null) {
                    $payarray[$cont]['payment_method'] = $paymentmethod->name;
                } else {
                    $payarray[$cont]['payment_method'] = 'N/A';
                }

                $cont++;
            }

            if ($pay['transaction_status'] == 'Approved') {
                $totalpay = $totalpay + $pay->amount;
            }

        }

        //////////////////////////////////// Taxes Avalara (PDF)
        $query = $this->avalaraInvoiceCurrent;
        ////////////////////////////////////////////////////////

        $cont = 0;
        $avalaraTaxesDeFinito = [];
        $index = 0;
        if ($query != null) {
            $query_2 = AvalaraTax::where('avalara_invoice_id', '=', $query->id)
                ->selectRaw('name, lvl, sum(amount) as total, item_id')
                ->groupBy('avalara_taxes.name', 'avalara_taxes.lvl')
                ->get();

            foreach ($query_2 as $q2) {
                $avalaraTaxesDeFinito[$cont]['name'] = $q2->name;
                $avalaraTaxesDeFinito[$cont]['lvl'] = $q2->lvl;
                $avalaraTaxesDeFinito[$cont]['total'] = $q2->total;
                //
                $item_ids = AvalaraTax::where('name', '=', $q2->name)->where('lvl', '=', $q2->lvl)->where('avalara_invoice_id', '=', $query->id)->get()->pluck('item_id');
                $avalaraTaxesDeFinito[$cont]['items'] = Item::whereIn('id', $item_ids)->get()->toArray();
                $cont++;
            }

            foreach ($avalaraTaxesDeFinito as $array) {
                if ($array['lvl'] == 0) {
                    $avalaraTaxesDeFinito[$index]['lvl'] = 'Federal';
                }
                if ($array['lvl'] == 1) {
                    $avalaraTaxesDeFinito[$index]['lvl'] = 'State';
                }
                if ($array['lvl'] == 2) {
                    $avalaraTaxesDeFinito[$index]['lvl'] = 'Country';
                }
                if ($array['lvl'] == 3) {
                    $avalaraTaxesDeFinito[$index]['lvl'] = 'City';
                }
                if ($array['lvl'] == 4) {
                    $avalaraTaxesDeFinito[$index]['lvl'] = 'Unincorporated';
                }
                $index++;
            }
        }

        $arrayitm = [];
        $cont = 0;

        if ($this->pbx_service_id != null) {
            $service = PbxServices::where('id', $this->pbx_service_id)->first();

            if ($service != null) {

                $pbxpac = PbxPackages::where('id', $service->pbx_package_id)->first();

                if ($pbxpac != null) {
                    $listgroup = ProfileDidCustomDidGroups::where('profile_did_id', $pbxpac->template_did_id)->whereNULL('deleted_at')->pluck('custom_did_group_id');

                    foreach ($listgroup as $tp) {

                        $customname = CustomDidGroups::where('id', $tp)->first();
                        $pop = TollFreeCustomDidGroup::where('custom_did_group_id', $tp)->pluck('toll_free_did_id');

                        if ($customname != null && count($pop) != 0) {
                            $arrayitm[$cont]['name'] = $customname->name;
                            $arrayitm[$cont]['list'] = $pop;
                            $cont++;
                        }
                    }

                }
            }

        }

        // late_fees array

        // Obtiene todos los cargos por mora asociados con la factura donde 'deleted_at' es NULL.
        $late_fees = InvoiceLateFee::where('invoice_id', $this->id)
            ->whereNull('deleted_at')
            ->get();

        // Calcula la suma total de los cargos por mora utilizando el método sum() de la colección.
        $total_fees = $late_fees->sum('total');

        //
        $servicecnumber = null;
        if ($this->customer_packages_id != null) {
            $serviceC = CustomerPackage::where('id', $this->customer_packages_id)->first();
            if ($serviceC != null) {
                $servicecnumber = $serviceC->code;
            }
        }

        $pbx_service_detail = PbxServicesDetail::where('invoice_id', '=', $this->id)->count();

        if ($pbx_service_detail > 0) {
            $pbx_serv_detail_count_extension = PbxServicesDetail::where('invoice_id', '=', $this->id)->first('count_extension');
            $pbx_serv_detail_count_did = PbxServicesDetail::where('invoice_id', '=', $this->id)->sum('count_did');

            $this->count_extensions = $pbx_serv_detail_count_extension;
            $this->count_extensions = $pbx_serv_detail_count_did;
        } else {
            $invoice_count_extension = Invoice::where('id', '=', $this->id)->first('count_extension');
            $invoice_count_did = Invoice::where('id', '=', $this->id)->first('count_did');

            $this->count_extensions = $invoice_count_extension;
            $this->count_extensions = $invoice_count_did;
        }

        // invoice_pbx_extension_detail (isEdited == 1 "Extensions")
        $invoice_pbx_extension_detail = DB::table('invoice_pbx_extension_detail')
            ->where('invoice_id', $this->id)
            ->where('deleted_at', null)
            ->get();

        $total_invoice_pbx_extension_detail = 0.00;
        if ($invoice_pbx_extension_detail->isNotEmpty()) {
            foreach ($invoice_pbx_extension_detail as $extension) {
                $total_invoice_pbx_extension_detail += $extension->total;
            }
        }

        // Dids - (isEdited == 0) -> invoice_pbx_did_detail
        $invoice_dids = self::findInvoiceDids($this->id);

        // Dids - (isEdited == 1) -> invoice_pbx_did_detail
        $invoice_pbx_did_detail = DB::table('invoice_pbx_did_detail')
            ->where('invoice_id', $this->id)
            ->where('deleted_at', null)
            ->get();

        $total_invoice_pbx_did_detail = 0.00;
        if ($invoice_pbx_did_detail->isNotEmpty()) {
            foreach ($invoice_pbx_did_detail as $did) {
                $total_invoice_pbx_did_detail += $did->total;
            }
        }

        // Valida si currency existe, no tiene valor o es null
        if (empty($this->user->currency)) {
            // En caso de que sea null, usar el valor de $this->user->company_id
            // para consultar el modelo CompanySetting y asignar el valor encontrado
            // al currency_id del usuario en una sola operación.
            $this->user->currency_id = CompanySetting::where('company_id', $this->user->company_id)
                ->where('option', 'currency')
                ->value('value');

            // Si se encontró un valor para currency_id, actualizar el usuario
            if ($this->user->currency_id) {
                $this->user->save();
            }
        }

        $data = [
            'store' => $store,
            'tables_selected' => $tablesSelected,
            'cash_register' => $cashRegister,
            'invoice_contact' => $invoiceContact,
            'invoice' => $this,
            'user_creator' => User::where('id', $this->creator_id)->first(),
            'company_address' => $this->getCompanyAddress(),
            'shipping_address' => $this->getCustomerShippingAddress(),
            'billing_address' => $this->getCustomerBillingAddress(),
            'notes' => $this->getNotes(),
            'logo' => $logo ?? null,
            'labels' => $labels,
            'taxes' => $taxes,
            'user' => User::find($this->user_id),
            'colorInvoice' => $colorInvoice,
            'Footer' => $this->getFooter(),
            'PaymentsArray' => $payarray,
            // received and returned
            'received' => $received,
            'returned' => $returned,
            //
            'PaymentsTotal' => $totalpay,
            'lateFees' => $late_fees,
            'totalFees' => $total_fees,
            'listcustomdid' => $arrayitm,
            'servicecnumber' => $servicecnumber,
            // Extensions (invoice -> isEdited == 1)
            'invoice_pbx_extension_detail' => $invoice_pbx_extension_detail,
            'total_invoice_pbx_extension_detail' => $total_invoice_pbx_extension_detail,
            // Dids (invoice -> isEdited == 1)
            'dids_group' => $invoice_dids,
            'invoice_pbx_did_detail' => $invoice_pbx_did_detail,
            'total_invoice_pbx_did_detail' => $total_invoice_pbx_did_detail,
            //
            'avalaraTaxesDeFinito' => $avalaraTaxesDeFinito,
            //"items_with_avalara_taxes" => $items_with_avalara_taxes
        ];
        \Log::debug('invoice model 1929', ['data' => $data]);
        view()->share($data);
        $pdf = PDF::loadView('app.pdf.invoice.'.$invoiceTemplate->view);

        if ($isPdfPos) {
            return $pdf->setPaper([0, 0, 200, 600], 'portrait');
        } else {

            return $pdf->setPaper('a4', 'portrait');
        }

    }

    public static function findInvoiceDids($invoice_id)
    {
        $groups = InvoiceDid::where('invoice_id', $invoice_id)
            ->groupBy('custom_did_id')
            ->groupBy('price')
            ->select(DB::RAW('GROUP_CONCAT(pbx_did_number) as pbx_dids_number'), DB::RAW('count(*) as qty'), 'name_prefix', 'price', 'custom_did_id', 'template_did_name')->get();

        // set name_prefix
        foreach ($groups as $group) {
            $name_prefix = '';
            if ($group['name_prefix'] != null) {
                $name_prefix = $group['name_prefix'];
            } else {
                if ($group['custom_did_id'] != null) {
                    $custom_did_group = CustomDidGroup::where('id', $group['custom_did_id'])->first();
                    $name_prefix = $custom_did_group['name'];
                } else {
                    $name_prefix = $group['template_did_name'];
                }
            }
            $group['name_prefix'] = $name_prefix;
        }

        $cont_loop = 0;
        $conta_x = 0;
        $conta_y = 0;

        foreach ($groups as $group) {
            $dids = array_map('intval', explode(',', $group['pbx_dids_number']));
            $count_dids = count($dids);

            $dids_array = [];
            $_dids = [];
            $total = 0;

            foreach ($dids as $did) {
                array_push($dids_array, $did);
                $total += 1;
                $cont_loop++;

                if ($cont_loop == 4) {
                    $_dids[$conta_y] = $dids_array;
                    $conta_y++;
                    $cont_loop = 0;
                    $dids_array = [];
                } elseif ($count_dids == $total) {
                    $_dids[$conta_y] = $dids_array;
                    $conta_y++;
                    $cont_loop = 0;
                    $dids_array = [];
                }

            }
            $conta_x++;
            $group['dids'] = $_dids;
        }

        return $groups;
    }

    public function getCompanyAddress()
    {
        $format = CompanySetting::getSetting('invoice_company_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerShippingAddress()
    {
        $format = CompanySetting::getSetting('invoice_shipping_address_format', $this->company_id);

        return $this->getFormattedString($format);
    }

    public function getCustomerBillingAddress()
    {
        $format = CompanySetting::getSetting('invoice_billing_address_format', $this->company_id);

        return $this->getFormattedString($format, $this->addresses_id);
    }

    public function getNotes()
    {
        return $this->getFormattedString($this->notes);
    }

    public function getFooter()
    {
        $format = CompanySetting::getSetting('invoice_footer', $this->company_id);
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());

        $body = strtr($format, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function getExtraFields()
    {
        return [
            '{INVOICE_DATE}' => $this->formattedInvoiceDate,
            '{INVOICE_DUE_DATE}' => $this->formattedDueDate,
            '{INVOICE_NUMBER}' => $this->invoice_number,
            '{INVOICE_REF_NUMBER}' => $this->reference_number,
            '{INVOICE_LINK}' => url('/customer/invoices/pdf/'.$this->unique_hash),
            '{PAY_LINK}' => '<a href="'.url('/customer/payments/'.$this->id.'/create').'"
                class="button button-primary" target="_blank">'.trans('invoices.pay_now').'</a> <br/>',
            '{PAY_LINK_LOGIN}' => '<a href="'.url('/payments/invoices/'.$this->unique_hash.'/create').'"
            class="button button-primary" target="_blank">'.trans('invoices.pay_now').'</a>',
        ];
    }

    public function sendInvoiceNotice($data)
    {
        try {
            $data['invoice'] = $this->toArray();
            $data['user'] = $this->user->toArray();
            $data['company'] = Company::find($this->company_id);
            $data['body'] = $this->getEmailBody($data['body']);
            $data['subject'] = $this->removeAttributesHtml($this->getEmailBody($data['subject']));

            // Obtener o establecer el color predeterminado de la factura
            $colorInvoice = CompanySetting::getSetting('color_invoice', $this->company_id) ?? '#5851D8';
            $data['PRIMARY_COLOR'] = $colorInvoice;

            // Enviar correo principal
            Mail::to($data['to'])->queue(new SendInvoiceMail($data));

            // Enviar correo BCC si está configurado
            $bccEmail = CompanySetting::where('option', 'invoice_bbc_email')
                ->where('company_id', $this->company_id)
                ->value('value');

            if (! empty($bccEmail)) {
                Mail::to($bccEmail)->queue(new SendInvoiceMail($data));
            }

            // Enviar correo a super administradores con la opción habilitada
            $superAdmins = User::where('role', 'super admin')
                ->where('email_invoices', 1)
                ->where('company_id', $this->company_id)
                ->get();

            foreach ($superAdmins as $user) {
                Mail::to($user->email)->queue(new SendInvoiceMail($data));
            }
        } catch (\Exception $e) {
            \Log::error("Error al enviar la notificación de la factura: ".$e->getMessage());
            // Considera retornar una respuesta o manejar el error según sea necesario
        }

        return ['success' => true];
    }

    public function getEmailBody($body)
    {
        $values = array_merge($this->getFieldsArray(), $this->getExtraFields());

        $body = strtr($body, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }

    public function removeAttributesHtml($string)
    {
        $temp = str_replace('<p>', '', $string);
        $temp = str_replace('</p>', '', $temp);
        $temp = str_replace('<strong>', '', $temp);
        $temp = str_replace('</strong>', '', $temp);
        $temp = str_replace('<em>', '', $temp);
        $temp = str_replace('</em>', '', $temp);
        $temp = str_replace('<s>', '', $temp);
        $temp = str_replace('</s>', '', $temp);
        $temp = str_replace('<u>', '', $temp);
        $temp = str_replace('</u>', '', $temp);
        $temp = str_replace('<code>', '', $temp);
        $temp = str_replace('</code>', '', $temp);
        $temp = str_replace('<h1>', '', $temp);
        $temp = str_replace('</h1>', '', $temp);
        $temp = str_replace('<h2>', '', $temp);
        $temp = str_replace('</h2>', '', $temp);
        $temp = str_replace('<h3>', '', $temp);
        $temp = str_replace('</h3>', '', $temp);
        $temp = str_replace('<ul>', '', $temp);
        $temp = str_replace('</ul>', '', $temp);
        $temp = str_replace('<li>', '', $temp);
        $temp = str_replace('</li>', '', $temp);
        $temp = str_replace('<ol>', '', $temp);
        $temp = str_replace('</ol>', '', $temp);
        $temp = str_replace('<blockquote>', '', $temp);
        $temp = str_replace('</blockquote>', '', $temp);
        $temp = str_replace('<pre>', '', $temp);
        $temp = str_replace('</pre>', '', $temp);

        return $temp;
    }

    public function send($data)
    {
        $data['invoice'] = $this->toArray();
        $data['user'] = $this->user->toArray();
        $data['company'] = Company::find($this->company_id);
        $data['body'] = $this->getEmailBody($data['body']);
        $data['subject'] = $this->removeAttributesHtml($this->getEmailBody($data['subject']));
        $colorInvoice = CompanySetting::getSetting('color_invoice', $this->company_id);
        $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        $data['PRIMARY_COLOR'] = $colorInvoice;

        if ($this->status == Invoice::STATUS_DRAFT || $this->status == Invoice::STATUS_SAVE_DRAFT) {
            $this->status = Invoice::STATUS_SENT;
            $this->save_as_draft = false;
            $this->sent = true;
            $this->save();
        }

        Mail::to($data['to'])->send(new SendInvoiceMail($data));

        //////////// Invoice_bbc_email

        try {
            if ($this->company_id != null) {

                $value = CompanySetting::select('value')->where('option', 'invoice_bbc_email')->where('company_id', $this->company_id)->first();

                //Log::debug($value);
                if ($value != null && $value !== '') {
                    Mail::to($value->value)->send(new SendInvoiceMail($data));
                }

            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }

        // ENVIAR A USERS
        // buscar todos los user cuyo rol sea super admin en el campo role y que el campo email_invoice sea igual a 1
        $listUser = User::where('role', 'super admin')->where('email_invoices', 1)->where('company_id', $this->company_id)->get();

        foreach ($listUser as $user) {
            try {
                Mail::to($user->email)->send(new SendInvoiceMail($data));
            } catch (Throwable $th) {
                Log::error($th);
            }
        }

        // envio de contactos
        $contacts = Contacts::select('email')->where('customer_id', $this->user_id)->where('allow_receive_emails', 1)->where('email_invoices', 1)->get();
        foreach ($contacts as $key => $email) {
            if ($email != null && $email != '') {
                // enviar email
                Mail::to($email)->send(new SendInvoiceMail($data));
                // save emails logs
                // $this->saveEmailLog($email, $newTitle, $newMessage, $mailable_id, $company_id, $customer_id);
            }
        }
        ////////////

        /* $customer_id = Auth::user()->id;
        $customer = User::findOrFail($customer_id);
        $settings = CompanySetting::select('value', 'id')->where('option', 'pbx_creation_services')->where('company_id', $this->company_id)->get();
        $mailable_id = $settings[0]->id; */
        // save emails logs
        // $this->saveEmailLog($customer->email, $subject = '', $data['body'], $mailable_id, $this->company_id, $customer_id);

        return [
            'success' => true,
        ];
    }

    public function storeEmailLog(string $customerEmail, string $subject, string $message, int $mailable_id, int $company_id, int $customer_id, string $mailable)
    {
        // llenar objeto email log y almacenarlo
        $emailLog['from'] = 'service@careonecomm.com';
        $emailLog['from'] = config('mail.from.address');
        $emailLog['to'] = $customerEmail;
        $emailLog['subject'] = $subject;
        $emailLog['body'] = $message;
        $emailLog['mailable_type'] = $mailable;
        $emailLog['mailable_id'] = $mailable_id;
        $emailLog['company_id'] = $company_id;
        $emailLog['creator_id'] = $customer_id;
        // $dataEmail[] = $emailLog;
        EmailLog::create($emailLog);
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(InvoiceExtension::class);
    }

    public function extensionDetails(): HasMany
    {
        return $this->hasMany(InvoicePbxExtensionDetail::class);
    }

    public function dids(): HasMany
    {
        return $this->hasMany(InvoiceDid::class);
    }

    public function didDetails(): HasMany
    {
        return $this->hasMany(InvoicePbxDidDetail::class);
    }

    public function additionalCharges(): HasMany
    {
        return $this->hasMany(InvoiceAdditionalCharge::class);
    }

    public function pbxCdr(): HasMany
    {
        return $this->hasMany(CallDetailRegisterTotal::class);
    }

    public function pbxService(): BelongsTo
    {
        return $this->belongsTo(PbxServices::class);
    }

    public function scheduleLogs(): MorphMany
    {
        return $this->morphMany(ScheduleLog::class, 'model');
    }
}
