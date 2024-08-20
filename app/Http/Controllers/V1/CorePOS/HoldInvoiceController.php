<?php

namespace Crater\Http\Controllers\V1\CorePOS;

use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Crater\Http\Controllers\Controller;
use Crater\Models\Address;
use Crater\Models\CompanySetting;
use Crater\Models\CorePosHistory;
use Crater\Models\Country;
use Crater\Models\HoldContact;
use Crater\Models\HoldInvoice;
use Crater\Models\HoldItem;
use Crater\Models\HoldTable;
use Crater\Models\HoldTax;
use Crater\Models\InvoiceTemplate;
use Crater\Models\PaymentMethod;
use Crater\Models\State;
use Crater\Models\Store;
use Crater\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class HoldInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companyId = auth()->user()->company->id;
        $holdInvoices = HoldInvoice::with(['holdItems', 'holdTaxes', 'holdContact', 'holdTables.table'])
            ->where('company_id', $companyId)
            ->paginate();

        return response()->json([
            'success' => true,
            'hold_invoices' => $holdInvoices,
            'message' => 'All hold invoices'
        ]);
    }

    /**
     * @param  $taxes
     * @param  $taxesData
     * @param int $holdInvoiceId
     * @param String $description
     * @param int $cashRegisterId
     *
     * @return [type]
     *
     * funcion para validar si los registros existen tanto en la BD o lo que se esta enviando desde el front hay dos casos donde o se puede crear el registro o se puede realizar eliminado logico
     */
    public function validateTables($tables,  $tablesData, $holdInvoiceId, String $description, int $cashRegisterId, $customerId)
    {
        $tablesIds = [];
        $tablesDataIds = [];
        // Get the id from the tables array
        foreach ($tables as $table) {
            $tablesIds[] = $table->table_id;
        }

        // Get the id from the taxes_data array
        foreach ($tablesData as $tableData) {
            $tablesDataIds[] = $tableData['table_id'];
        }
        // Check if there are id in tables that are not in taxes_data
        foreach ($tablesIds as $tableId) {
            if (! in_array($tableId, $tablesDataIds)) {
                // Here you could write code to remove the element from tables with the id that is not in taxes_data
                foreach ($tables as $table) {
                    if ($table->table_id == $tableId) {
                        HoldTable::where('id', $table->id)->where('hold_invoice_id', $holdInvoiceId)->delete();
                        // CorePosHistory::recordTaxesEdit($taxData, $holdInvoiceId, $description, 'hold_tax_delete', $cashRegisterId, $customerId);
                    }
                }
            }
        }

        try {
            // Check if there are id in tables_data that are not in tables
            foreach ($tablesDataIds as $tablesDataId) {
                if (! in_array($tablesDataId, $tablesIds)) {
                    foreach ($tablesData as $tableData) {
                        if ($tableData['table_id'] == $tablesDataId) {
                            $tableData['hold_invoice_id'] = $holdInvoiceId;
                            HoldTable::create([
                                'hold_invoice_id' => $holdInvoiceId,
                                'table_id' => $tableData['table_id'],
                                'quantity' => $tableData['quantity']
                            ]);
                            // CorePosHistory::recordTaxesEdit($taxData, $holdInvoiceId, $description, 'hold_tax_create', $cashRegisterId, $customerId);
                        }
                    }
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::debug($th);
        }
    }

    /**
     * @param  $taxes
     * @param  $taxesData
     * @param int $holdInvoiceId
     * @param String $description
     * @param int $cashRegisterId
     *
     * @return [type]
     *
     * funcion para validar si los registros existen tanto en la BD o lo que se esta enviando desde el front hay dos casos donde o se puede crear el registro o se puede realizar eliminado logico
     */
    public function validateTaxes($taxes,  $taxesData, $holdInvoiceId, String $description, int $cashRegisterId, $customerId)
    {
        $taxesIds = [];
        $taxesDataIds = [];

        // Get the tax_type_id from the taxes array
        foreach ($taxes as $tax) {
            $taxesIds[] = $tax['tax_type_id'];
        }

        // Get the tax_type_id from the taxes_data array
        foreach ($taxesData as $taxData) {
            $taxesDataIds[] = $taxData['tax_type_id'];
        }

        // Check if there are tax_type_id in taxes that are not in taxes_data
        foreach ($taxesIds as $taxId) {
            if (! in_array($taxId, $taxesDataIds)) {
                // Here you could write code to remove the element from taxes with the tax_type_id that is not in taxes_data
                foreach ($taxes as $tax) {
                    if ($tax['tax_type_id'] == $taxId) {
                        HoldTax::where('tax_type_id', $taxId)->where('hold_invoice_id', $holdInvoiceId)->delete();
                        CorePosHistory::recordTaxesEdit($taxData, $holdInvoiceId, $description, 'hold_tax_delete', $cashRegisterId, $customerId);
                    }
                }
            }
        }

        // Check if there are tax_type_id in taxes_data that are not in taxes
        foreach ($taxesDataIds as $taxDataId) {
            if (! in_array($taxDataId, $taxesIds)) {
                foreach ($taxesData as $taxData) {
                    if ($taxData['tax_type_id'] == $taxDataId) {
                        $taxData['hold_invoice_id'] = $holdInvoiceId;
                        HoldTax::create($taxData);
                        CorePosHistory::recordTaxesEdit($taxData, $holdInvoiceId, $description, 'hold_tax_create', $cashRegisterId, $customerId);
                    }
                }
            }
        }
    }

    /**
     * @param  $items
     * @param  $itemsData
     * @param  int $holdInvoiceId
     * @param String $description
     * @param int $cashRegisterId
     *
     * @return [type]
     *
     *  funcion para validar si los registros existen tanto en la BD o lo que se esta enviando desde el front hay dos casos donde o se puede crear el registro o se puede realizar eliminado logico
     */
    public function validateItems($items,  $itemsData, $holdInvoiceId, String $description, int $cashRegisterId, $userId)
    {
        $itemsIds = [];
        $itemsDataIds = [];

        // Get the tax_type_id from the taxes array
        foreach ($items as $item) {
            $itemsIds[] = $item['item_id'];
        }

        // Get the item_id from the taxes_data array
        foreach ($itemsData as $itemData) {
            $itemsDataIds[] = $itemData['item_id'];
        }

        foreach ($itemsIds as $itemId) {
            if (! in_array($itemId, $itemsDataIds)) {
                // Here you could write code to remove the element from taxes with the tax_type_id that is not in taxes_data
                foreach ($items as $item) {
                    if ($item['item_id'] == $itemId) {
                        HoldItem::where('item_id', $itemId)->where('hold_invoice_id', $holdInvoiceId)->delete();
                        CorePosHistory::recordItemsEdit($itemData, $holdInvoiceId, $description, 'hold_item_delete', $cashRegisterId,  $userId);
                    }
                }
            }
        }

        // Check if there are tax_type_id in taxes_data that are not in taxes
        foreach ($itemsDataIds as $itemDataId) {
            if (! in_array($itemDataId, $itemsIds)) {
                foreach ($itemsData as $itemData) {
                    if ($itemData['item_id'] == $itemDataId) {
                        $itemData['hold_invoice_id'] = $holdInvoiceId;

                        HoldItem::create($itemData);

                        CorePosHistory::recordItemsEdit($itemData, $holdInvoiceId, $description, 'hold_item_create', $cashRegisterId, $userId);
                    }
                }
            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            $data = $request->all();
            $holdExists = false;

            if (array_key_exists("hold_invoice_id", $data) == false) {
                $ob = HoldInvoice::where('description', $data['description'])->whereNull('deleted_at')->first();
                if ($ob != null) {
                    $data['hold_invoice_id'] = $ob->id;
                } else {
                    $data['hold_invoice_id'] = null;
                }
            }

            $printop = false;
            if (array_key_exists("print_pdf", $data)) {
                if ($data["print_pdf"] == true || $data["print_pdf"] == 1) {
                    $printop = true;
                }
            }

            $existsHoldInvoice = HoldInvoice::where('description', $data['description'])->whereNull('deleted_at')->exists();
            // where('id', $data['hold_invoice_id'])->

            // si el registro existe se pasa a actualizar todos los registros de las tablas Hold y crear los registros respectivos de la tabla core_pos_history
            if ($existsHoldInvoice) {
                $data['hold_invoice_id'] = HoldInvoice::where('description', $data['description'])->whereNull('deleted_at')->pluck('id')->first();
                $holdExists = true;
                $data['action'] = $printop == false ? 'hold_edited' : 'hold_edited_print';
                HoldContact::where('hold_invoice_id',  $data['hold_invoice_id'])->whereNull('deleted_at')->forceDelete();

                // parte para llamar la funcion que valida si los registros existen en la BD o se estan enviando por primera vez desde el front
                $tables = DB::table('hold_tables')->where('hold_invoice_id', $data['hold_invoice_id'])->whereNull('deleted_at')->get();
                $this->validateTables($tables, $data['tables_selected'], $data['hold_invoice_id'], $data['description'], $data['cash_register_id'], $data['user_id']);

                foreach ($tables as $table1) {
                    foreach ($data['tables_selected'] as $table2) {
                        if ($table1->table_id == $table2['table_id']) {
                            if ($table1->quantity != $table2['quantity']) {
                                DB::table('hold_tables')->whereNull('deleted_at')->where('id', $table1->id)->update([
                                    'quantity' => $table2['quantity']
                                ]);

                                // CorePosHistory::recordTaxesEdit($table2, $data['hold_invoice_id'], $data['description'], 'hold_edit_tax_percent', $data['cash_register_id'],  $data['user_id']);
                            } else {

                                // CorePosHistory::recordTaxesEdit($table2, $data['hold_invoice_id'], $data['description'], 'hold_edit_tax_without_change', $data['cash_register_id'], $data['user_id']);
                            }
                        }
                    }
                }

                $taxes = HoldTax::where('hold_invoice_id',  $data['hold_invoice_id'])->get();
                // parte para llamar la funcion que valida si los registros existen en la BD o se estan enviando por primera vez desde el front
                $this->validateTaxes($taxes, $data['taxes'], $data['hold_invoice_id'], $data['description'], $data['cash_register_id'], $data['user_id']);
                foreach ($taxes as $tax1) {
                    foreach ($data['taxes'] as $tax2) {
                        if ($tax1['tax_type_id'] == $tax2['tax_type_id']) {
                            if ($tax1['percent'] != $tax2['percent']) {

                                HoldTax::where('id', $tax1['id'])->update([
                                    'percent' => $tax2['percent']
                                ]);

                                CorePosHistory::recordTaxesEdit($tax2, $data['hold_invoice_id'], $data['description'], 'hold_edit_tax_percent', $data['cash_register_id'],  $data['user_id']);
                            } elseif ($tax1['amount'] != $tax2['amount']) {

                                HoldTax::where('id', $tax1['id'])->update([
                                    'amount' => $tax2['amount']
                                ]);

                                CorePosHistory::recordTaxesEdit($tax2, $data['hold_invoice_id'], $data['description'], 'hold_edit_tax_amount', $data['cash_register_id'], $data['user_id']);
                            } else {

                                CorePosHistory::recordTaxesEdit($tax2, $data['hold_invoice_id'], $data['description'], 'hold_edit_tax_without_change', $data['cash_register_id'], $data['user_id']);
                            }
                        }
                    }
                }

                // parte para llamar la funcion que valida si los registros existen en la BD o se estan enviando por primera vez desde el front
                $holdItems = HoldItem::where('hold_invoice_id', $data['hold_invoice_id'])->get();
                $this->validateItems($holdItems, $data['items'], $data['hold_invoice_id'], $data['description'], $data['cash_register_id'], $data['user_id']);

                foreach ($holdItems as $item1) {
                    foreach ($data['items'] as $item2) {
                        if ($item1['item_id'] == $item2['item_id']) {
                            // if ($item1['quantity'] != $item2['quantity'] || $item1['price'] != $item2['price'] || $item1['sub_total'] != $item2['sub_total']) {
                            if ($item1['quantity'] != $item2['quantity']) {
                                HoldItem::where('id', $item1['id'])->update([
                                    'name' => $item2['name'],
                                    'description' => $item2['description'],
                                    'discount' => $item2['discount'],
                                    'discount_val' => $item2['discount_val'],
                                    'quantity' => $item2['quantity'],
                                    'price' => $item2['price'],
                                    'total' => $item2['total'],
                                    'tax' => $item2['tax'],
                                ]);

                                CorePosHistory::recordItemsEdit($item2, $data['hold_invoice_id'], $data['description'], 'hold_edit_item', $data['cash_register_id'], $data['user_id']);
                            } else {
                                CorePosHistory::recordItemsEdit($item2, $data['hold_invoice_id'], $data['description'], 'hold_item_without_changes', $data['cash_register_id'], $data['user_id']);
                            }
                        }
                    }
                }

                HoldInvoice::where('id', $data['hold_invoice_id'])->where('description', $data['description'])->whereNull('deleted_at')->update([
                    'total' => $data['total'],
                    'due_amount' => $data['due_amount'],
                    'sub_total' => $data['sub_total'],
                    'tax' => $data['tax'],
                    'discount_type' => $data['discount_type'],
                    'discount' => $data['discount'],
                    'discount_val' => $data['discount_val'],
                    'tip_type' => $data['tip_type'],
                    'tip' => $data['tip'],
                    'tip_val' => $data['tip_val'],
                    'notes' => $data['notes'],
                    'updated_at' => Carbon::now(),
                    'store_id' => $data['store_id']
                ]);

                if (! empty($data['contact'])) {
                    $data['contact']['hold_invoice_id'] = $data['hold_invoice_id'];
                    HoldContact::create($data['contact']);
                }
            } else {
                // parte para crear todos los registros en las tablas hold
                $data['action'] = $printop == false ? 'hold_created' : 'hold_created_print';
                $data['created_at'] = Carbon::now();
                $data['updated_at'] = Carbon::now();
                $data['company_id'] = auth()->user()->company->id;

                //SAVE INVOICE POS
                $invoice = HoldInvoice::create($data);

                // SAVE TAXES
                foreach ($data['taxes'] as $tax) {
                    $tax['hold_invoice_id'] = $invoice->id;
                    HoldTax::create($tax);
                }

                ///SAVE ITEM
                foreach ($data['items'] as $item) {
                    try {

                        $item['hold_invoice_id'] = $invoice->id;
                        HoldItem::create($item);
                    } catch (\Throwable $th) {
                        \Log::debug($th);
                    }
                }

                // SAVE TABLES
                foreach ($data['tables_selected'] as $table) {
                    try {
                        HoldTable::create([
                            'hold_invoice_id' => $invoice->id,
                            'table_id' => $table['id'],
                            'quantity' => $table['quantity']
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                        Log::debug($th);
                    }
                }

                if (! empty($data['contact'])) {
                    $data['contact']['hold_invoice_id'] = $invoice->id;
                    HoldContact::create($data['contact']);
                }

                $invoiceHoldId = $invoice->id;
            }

            // parte para crear el registro si es por primera vez crea el registro de todos los historicos
            $holdId = $data['hold_invoice_id'] != null ? $data['hold_invoice_id'] : $invoiceHoldId;
            CorePosHistory::recordHistory($data, $holdId, $holdExists);


            return response()->json([
                'success' => true,
                'message' => 'save hold invoice successfuly'
            ]);
        } catch (\Throwable $th) {
            Log::debug('hold invoice controller 85', ['error' => $th]);

            return response()->json([
                'success' => false,
                'message' => 'An error ocurred'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    public function deleteHoldInvoice(Request $request)
    {

        try {
            $id = $request->id;
            $data = CorePosHistory::where('hold_id', $id)->get();

            HoldContact::where('hold_invoice_id', $id)->delete();
            HoldTax::where('hold_invoice_id', $id)->delete();
            HoldItem::where('hold_invoice_id', $id)->delete();
            HoldInvoice::where('id', $id)->delete();

            foreach ($data as $register) {
                CorePosHistory::create([
                    "document_number" => $register->document_number,
                    "creator_id" => $register->creator_id,
                    "payment_id" => $register->payment_id,
                    "invoice_id" => $register->invoice_id,
                    "hold_id" => $register->hold_id,
                    "action" => 'hold_delete',
                    "date_time" => $register->date_time,
                    "item_id" => $register->item_id,
                    "item_price" => $register->item_price,
                    "item_total" => $register->item_total,
                    "item_quantity" => $register->item_quantity,
                    "discount_type" => $register->discount_type,
                    "discount_amount" => $register->discount_amount,
                    "tax_id" => $register->tax_id,
                    "tax_type_percent" => $register->tax_type_percent,
                    "tax_type_amount" => $register->tax_type_amount,
                    "customer_id" => $register->customer_id,
                    "cash_register_id" => $register->cash_register_id,
                    "notes" => $register->notes,
                    "tables" => $register->tables,
                    "qty_persons" => $register->qty_persons,
                    "company_id" => $register->company_id
                ]);
            }


            return response()->json([
                'success' => true,
                'message' => 'Resource deleted'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Resource deleted',
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    }

    public function getPdfHoldInvoice(Request $request)
    {

        try {

            $this->store($request);
            $data = $request['data'];
            $taxTypes = [];
            $taxes = [];
            $labels = [];



            $isPdfPos = $request['is_pdf_pos'];
            $isInvoicePos = $request['is_invoice_pos'];

            if ($isInvoicePos) {

                $invoiceContact = null;
                $cashRegister = DB::table('cash_register')->where('id', $request['cash_register_id'])->first();
                $store = Store::where('id', $request['store_id'])->first();
            } else {
                $cashRegister = null;
                $invoiceContact = null;
            }
            if ($isPdfPos) {

                $invoiceTemplate = InvoiceTemplate::where('view', 'template_pos')->first();
                $tablesSelected = [];
            } else {
                $tablesSelected = [];
                $invoiceTemplate = InvoiceTemplate::find($request['invoice_template_id']);
            }

            $company = User::find($request['user_id']);
            $invoiceData = [];
            $invoiceData['invoice'] = $request->all();
            $invoiceData['invoice']['user'] = $company;
            $logo = $company->logo_base_64;

            $invoiceData['invoice']['company'] = auth()->user()->company;
            $colorInvoice = CompanySetting::getSetting('color_invoice', auth()->user()->company->id);
            $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
            $solaNOmultipe = PaymentMethod::where("is_multiple", 0)->pluck("id")->toarray();

            $payarray = [];
            $cont = 0;
            $totalpay = 0;
            $solaNOmultipe = PaymentMethod::where("is_multiple", 1)->pluck("id")->toarray();
            $received = null;
            $returned = null;



            $dataArray = [
                'store' => $store,
                'tables_selected' => $tablesSelected,
                'cash_register' => $cashRegister,
                'invoice_contact' => $invoiceContact,
                'invoice' => $invoiceData['invoice'],
                'user_creator' => auth()->user(),
                'company_address' => $this->getFormattedString(CompanySetting::getSetting('invoice_company_address_format', auth()->user()->company->id)),
                'shipping_address' => $this->getFormattedString(CompanySetting::getSetting('invoice_shipping_address_format', auth()->user()->company->id)),
                'billing_address' => $this->getFormattedString(CompanySetting::getSetting('invoice_billing_address_format', auth()->user()->company->id)),
                'logo' => $logo ?? null,
                'labels' => $labels,
                'taxes' => $taxes,
                'colorInvoice' => $colorInvoice,
                'Footer' => $this->getFooter(),
                'PaymentsArray' => $payarray,
                'received' => $received,
                'returned' => $returned,
                'PaymentsTotal' => $totalpay,

            ];
            view()->share($dataArray);
            $pdf = PDF::loadView('app.pdf.invoice.template_hold_invoice');
            $reportPdf = $pdf->setPaper([0, 0, 200, 600], 'portrait');
            $pdfBase64 = base64_encode($reportPdf->output());

            // Devuelve el PDF Base64 como respuesta
            return response()->json(['pdfBase64' => $pdfBase64]);
        } catch (\Throwable $th) {
            Log::debug('error hold model 382', ['error' => $th]);
        }
    }

    public function getFormattedString($format, $invoice = null)
    {
        $values = array_merge($this->getFieldsArray($invoice));

        $str = nl2br(strtr($format, $values));

        $str = preg_replace('/{(.*?)}/', '', $str);

        $str = preg_replace("/<[^\/>]*>([\s]?)*<\/[^>]*>/", '', $str);

        $str = str_replace("<p>", "", $str);

        $str = str_replace("</p>", "</br>", $str);

        return $str;
    }

    public function getFieldsArray($invoice = null)
    {
        $customer = auth()->user();
        $shippingAddress = $customer->shippingAddress ?? new Address();
        $billingAddress = $customer->billingAddress ?? new Address();
        $companyAddress = auth()->user()->company->address ?? new Address();
        $companyAddress->state = State::find($companyAddress->state_id);
        $companyAddress->country = Country::find($companyAddress->country_id);

        //Log::debug($companyAddress);

        if ($invoice != null) {
            $direccion = Address::where("id", $invoice)->first();
            if ($direccion != null) {
                //Log::debug($direccion);
                $billingAddress = $direccion;
            }
        }

        $state = "";
        $state2 = "";
        if ($billingAddress->state_id != null) {
            $obj = State::where("id", $billingAddress->state_id)->first();
            if ($obj != null) {
                $state = $obj->name;
            }
        }

        if ($shippingAddress->state_id != null) {
            $obj = State::where("id", $shippingAddress->state_id)->first();
            if ($obj != null) {
                $state2 = $obj->name;
            }
        }

        $fields = [
            '{SHIPPING_ADDRESS_NAME}' => $shippingAddress->name,
            '{SHIPPING_COUNTRY}' => $shippingAddress->country_name,
            '{SHIPPING_STATE}' => $state2,
            '{SHIPPING_CITY}' => $shippingAddress->city,
            '{SHIPPING_ADDRESS_STREET_1}' => $shippingAddress->address_street_1,
            '{SHIPPING_ADDRESS_STREET_2}' => $shippingAddress->address_street_2,
            '{SHIPPING_PHONE}' => $shippingAddress->phone,
            '{SHIPPING_ZIP_CODE}' => $shippingAddress->zip,
            '{BILLING_ADDRESS_NAME}' => $billingAddress->name,
            '{BILLING_COUNTRY}' => $billingAddress->country_name,
            '{BILLING_STATE}' => $state,
            '{BILLING_CITY}' => $billingAddress->city,
            '{BILLING_ADDRESS_STREET_1}' => $billingAddress->address_street_1,
            '{BILLING_ADDRESS_STREET_2}' => $billingAddress->address_street_2,
            '{BILLING_PHONE}' => $billingAddress->phone,
            '{BILLING_ZIP_CODE}' => $billingAddress->zip,
            '{COMPANY_NAME}' => auth()->user()->company->name,
            '{COMPANY_COUNTRY}' => $companyAddress->country->name ?? null,
            '{COMPANY_STATE}' => $companyAddress->state->name ?? null,
            '{STATE_CODE}' => $companyAddress->state->code ?? null,
            '{COMPANY_CITY}' => $companyAddress->city,
            '{COMPANY_ADDRESS_STREET_1}' => $companyAddress->address_street_1,
            '{COMPANY_ADDRESS_STREET_2}' => $companyAddress->address_street_2,
            '{COMPANY_PHONE}' => $companyAddress->phone,
            '{COMPANY_ZIP_CODE}' => $companyAddress->zip,
            '{CONTACT_DISPLAY_NAME}' => $customer->name,
            '{PRIMARY_CONTACT_NAME}' => $customer->contact_name,
            '{PRIMARY_COLOR}' => $this->getPrimaryColor(auth()->user()->company->id),
            '{CONTACT_EMAIL}' => $customer->email,
            '{CONTACT_PHONE}' => $customer->phone,
            '{CONTACT_WEBSITE}' => $customer->website,


        ];

        //         $customFields = $fields;
        //         $customerCustomFields = auth()->user()->fields;
        // Log::debug( auth()->user()->fields);
        //         foreach ($customFields as $customField) {
        //             $fields['{' . $customField->customField->slug . '}'] = $customField->defaultAnswer;
        //         }

        //         foreach ($customerCustomFields as $customField) {
        //             $fields['{' . $customField->customField->slug . '}'] = $customField->defaultAnswer;
        //         }

        return $fields;
    }

    public function getPrimaryColor($company_id = null)
    {
        if (isset($company_id)) {
            $colorInvoice = CompanySetting::getSetting('color_invoice', $company_id);

            return $colorInvoice = $colorInvoice ? $colorInvoice : '#5851D8';
        }
    }

    public function getFooter()
    {
        $format = CompanySetting::getSetting('invoice_footer', auth()->user()->company->id);
        $values = array_merge($this->getFieldsArray());

        $body = strtr($format, $values);

        return preg_replace('/{(.*?)}/', '', $body);
    }
}
