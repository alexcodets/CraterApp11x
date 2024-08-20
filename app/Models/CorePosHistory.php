<?php

namespace Crater\Models;

use Cache;
use Carbon\Carbon;
use Crater\CorePos\Models\PosCashRegister;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CorePosHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_number',
        'creator_id',
        'payment_id',
        'invoice_id',
        'hold_id',
        'action',
        'date_time',
        'item_id',
        'item_price',
        'item_total',
        'item_quantity',
        'discount_type',
        'discount_amount',
        'tax_id',
        'tax_type_percent',
        'tax_type_amount',
        'customer_id',
        'cash_register_id',
        'notes',
        'tables',
        'qty_persons',
    ];

    protected $appends = [
        'formattedCreatedAt',
        'formattedDocumentNumberType',
        'formattedDocumentItems',
        'formattedDocumentCreator',
        'formattedDocumentCustomer',
        'formattedDocumentCashRegister',
        'formattedDocumentColorAction',
        'formattedDocumentTaxtype',
        'formattedDocumentLangAction',
    ];

    /**
     * function main for initialized
     * @param array $data
     * @param String $documentId
     * @param bool $exists
     *
     * @return [type]
     */
    public function recordHistory(array $data, String $documentId, $exists = false)
    {

        if ($exists) {
            self::validateIfHoldChange($data, $documentId);
        } else {
            self::registerCorePosHistory($data, $documentId);
        }
    }

    /**
     * @param array $data
     * @param String $documentId
     *
     * @return [type]
     */
    public function registerCorePosHistory(array $data, String $documentId)
    {
        CorePosHistory::create([
            'document_number' => $data['description'],
            'hold_id' => $documentId,
            'creator_id' => auth()->user()->id,
            'company_id' => auth()->user()->company->id,
            'action' => $data['action'],
            'cash_register_id' => $data['cash_register_id'],
            'customer_id' => $data['user_id'],
            'created_at' => Carbon::now(),
        ]);

        self::recordItems($data, $documentId);
        self::recordDiscount($data, $documentId);
        self::recordTaxes($data, $documentId);
        self::recordNotes($data, $documentId);
        self::recordTables($data, $documentId);
    }

    /**
     * @param array $item
     * @param String $documentId
     * @param String $documentNumber
     * @param String $action
     * @param mixed $cashRegisterId
     *
     * @return [type]
     */
    public function recordItemsEdit(array $item, String $documentId, String $documentNumber, String $action, $cashRegisterId, $userId)
    {
        // cambio de key
        if (array_key_exists('item_id', $item)) {
            $item['id'] = $item['item_id'];
        }
        CorePosHistory::create([
            'document_number' => $documentNumber,
            'hold_id' => $documentId,
            'creator_id' => auth()->user()->id,
            'company_id' => auth()->user()->company->id,
            'customer_id' => $userId,
            'action' => $action,
            'cash_register_id' => $cashRegisterId,
            'created_at' => Carbon::now(),
            'item_id' => $item['id'],
            'item_price' => $item['price'],
            'item_quantity' => $item['quantity'],
            'item_total' => $item['quantity'] * $item['price'],
        ]);
    }

    /**
     * @param array $data
     * @param string $documentId
     *
     * @return [type]
     */
    public function recordItems(array $data, string $documentId)
    {

        $items = $data['items'];
        foreach ($items as $item) {
            if (array_key_exists('item_id', $item)) {
                $item['id'] = $item['item_id'];
            }
            CorePosHistory::create([
                'document_number' => $data['description'],
                'hold_id' => $documentId,
                'creator_id' => auth()->user()->id,
                'company_id' => auth()->user()->company->id,
                'customer_id' => $data['user_id'],
                'action' => "hold_create_item",
                'cash_register_id' => $data['cash_register_id'],
                'created_at' => Carbon::now(),
                'item_id' => $item['id'],
                'item_price' => $item['price'],
                'item_quantity' => $item['quantity'],
                'item_total' => $item['quantity'] * $item['price'],
            ]);
        }

        return true;
    }

    /**
     * @param array $data
     * @param String $documentId
     *
     * @return [type]
     */
    public function recordDiscount(array $data, String $documentId)
    {
        if ($data['discount'] != 0) {
            CorePosHistory::create([
                'document_number' => $data['description'],
                'hold_id' => $documentId,
                'creator_id' => auth()->user()->id,
                'company_id' => auth()->user()->company->id,
                'customer_id' => $data['user_id'],
                'action' => "hold_create_discount",
                'discount_type' => $data['discount_type'],
                'discount_amount' => $data['discount'],
                'cash_register_id' => $data['cash_register_id'],
                'created_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * @param Array $data
     * @param String $documentId
     * @param String $documentNumber
     * @param String $action
     *
     * @return [type]
     */
    public static function recordTaxesEdit(array $data, String $documentId, String $documentNumber, String $action, $cashRegisterId, $customerId)
    {
        CorePosHistory::create([
            'document_number' => $documentNumber,
            'hold_invoice_id' => $documentId,
            'creator_id' => auth()->user()->id,
            'company_id' => auth()->user()->company->id,
            'customer_id' => $customerId,
            'action' => $action,
            'tax_id' => $data['tax_type_id'],
            'tax_type_percent' => $data['percent'],
            'tax_type_amount' => $data['amount'] * 100,
            'cash_register_id' => $cashRegisterId,
            'created_at' => Carbon::now(),
        ]);
    }

    /**
     * @param array $data
     * @param String $documentId
     *
     * @return [type]
     */
    public function recordTaxes(array $data, String $documentId)
    {
        $taxes = $data['taxes'];

        if (! empty($taxes)) {
            //Log::debug('taxes 122', ['item' => $taxes]);
            foreach ($taxes as $tax) {
                CorePosHistory::create([
                    'document_number' => $data['description'],
                    'hold_id' => $documentId,
                    'creator_id' => auth()->user()->id,
                    'company_id' => auth()->user()->company->id,
                    'customer_id' => $data['user_id'],
                    'action' => "hold_create_tax",
                    'tax_id' => $tax['tax_type_id'],
                    'tax_type_percent' => $tax['percent'],
                    'tax_type_amount' => $tax['amount'] * 100,
                    'cash_register_id' => $data['cash_register_id'],
                    'created_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * @param array $data
     * @param String $documentId
     *
     * @return [type]
     */
    public function recordNotes(array $data, String $documentId)
    {

        $notes = $data['notes'];
        if ($notes != null) {
            CorePosHistory::create([
                'document_number' => $data['description'],
                'hold_id' => $documentId,
                'creator_id' => auth()->user()->id,
                'company_id' => auth()->user()->company->id,
                'customer_id' => $data['user_id'],
                'action' => "hold_create_note",
                'cash_register_id' => $data['cash_register_id'],
                'notes' => $data['notes'],
                'created_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * @param array $data
     * @param String $documentId
     *
     * @return [type]
     */
    public function recordTables(array $data, String $documentId)
    {

        $tablesData = $data['tables_selected'];
        if (! empty($tablesData)) {
            $tablesName = implode(', ', array_map(function ($name) {
                return $name['name'];
            }, $tablesData));
            $quantityPersons = array_column($tablesData, 'quantity');
            CorePosHistory::create([
                'document_number' => $data['description'],
                'hold_id' => $documentId,
                'creator_id' => auth()->user()->id,
                'company_id' => auth()->user()->company->id,
                'customer_id' => $data['user_id'],
                'action' => "hold_create_table",
                'cash_register_id' => $data['cash_register_id'],
                'tables' => $tablesName,
                'qty_persons' => array_sum($quantityPersons),
                'created_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * @param array $data
     * @param String $documentId
     *
     * @return [type]
     */
    public function validateIfHoldChange(array $data, String $documentId)
    {

        CorePosHistory::create([
            'document_number' => $data['description'],
            'hold_id' => $documentId,
            'creator_id' => auth()->user()->id,
            'company_id' => auth()->user()->company->id,
            'customer_id' => $data['user_id'],
            'action' => $data['action'],
            'cash_register_id' => $data['cash_register_id'],
            'created_at' => Carbon::now(),
        ]);

        if (! (int) $data['discount'] == 0) {

            $resultDiscount = DB::table('core_pos_histories')
                ->where('document_number', $data['description'])
                ->where('hold_id', $data['hold_invoice_id'])
                ->where('discount_type', $data['discount_type'])
                ->where('discount_amount', $data['discount'])
                ->get();

            if ($resultDiscount->isEmpty()) {
                $data['action'] = 'hold_edit_discount';
                CorePosHistory::recordDiscount($data, $data['hold_invoice_id']);
            } else {
                $data['action'] = 'hold_editnot_discount';
                CorePosHistory::recordDiscount($data, $data['hold_invoice_id']);
            }
        }

        $resultNotes = DB::table('core_pos_histories')
            ->where('document_number', $data['description'])
            ->where('hold_id', $documentId)
            ->where('notes', $data['notes'])
            ->get();

        // //Log::debug('discount result', ['item' => $resultNotes]);
        if ($resultNotes->isEmpty()) {
            $data['action'] = 'hold_edit_notes';
            CorePosHistory::recordNotes($data, $data['hold_invoice_id']);
            $data['action'] = '';
        } else {
            $data['action'] = 'hold_editnot_notes';
            CorePosHistory::recordNotes($data, $data['hold_invoice_id']);
            $data['action'] = '';
        }

        if (! empty($data['tables_selected'])) {

            $tablesName = implode(', ', array_map(function ($name) {
                return $name['name'];
            }, $data['tables_selected']));
            $quantityPersons = array_column($data['tables_selected'], 'quantity');

            $resultTables = DB::table('core_pos_histories')
                ->where('document_number', $data['description'])
                ->where('hold_id', $documentId)
                ->where('tables', $tablesName)
                ->where('qty_persons', array_sum($quantityPersons))
                ->get();

            if ($resultTables->isEmpty()) {
                $data['action'] = 'hold_edit_tables';
                CorePosHistory::recordTables($data, $data['hold_invoice_id']);
                $data['action'] = '';
            } else {
                $data['action'] = 'hold_editnot_tables';
                CorePosHistory::recordTables($data, $data['hold_invoice_id']);
                $data['action'] = '';
            }
        }
    }

    public function scopePaginateData($query, $limit)
    {
        if ($limit == 'all') {
            return collect(['data' => $query->get()]);
        }

        return $query->paginate($limit);
    }

    public function getFormattedCreatedAtAttribute($value)
    {
        if (is_null($this->created_at)) {
            return null;
        }

        $dateFormat = Cache::remember('carbon_date_format_'.$this->company_id, 60 * 5, function () {
            return CompanySetting::getSetting('carbon_date_format', $this->company_id);
        });

        return Carbon::parse($this->created_at)->format('Y-m-d H:i:s');
    }

    public function getFormattedDocumentNumberTypeAttribute($value)
    {
        if ($this->hold_id != null && $this->invoice_id == null && $this->payment_id == null) {
            return "coreposhistory.hold_invoice";
        }

        if ($this->hold_id == null && $this->invoice_id != null && $this->payment_id == null) {
            return "coreposhistory.invoice";
        }

        if ($this->hold_id == null && $this->invoice_id == null && $this->payment_id != null) {
            return "coreposhistory.payments";
        }

        return "coreposhistory.general";
    }

    public function getFormattedDocumentItemsAttribute($value)
    {
        if ($this->item_id == null) {
            return null;
        }

        if ($this->item_id != null) {
            $item = Item::where("id", $this->item_id)->first();
            if ($item != null) {
                return $item->name;
            }

            return null;
        }

    }

    public function getFormattedDocumentTaxtypeAttribute($value)
    {
        if ($this->tax_id == null) {
            return null;
        }

        if ($this->tax_id != null) {
            $taxtype = TaxType::where("id", $this->tax_id)->first();
            if ($taxtype != null) {
                return $taxtype->name;
            }

            return null;
        }

    }

    public function getFormattedDocumentCreatorAttribute($value)
    {
        if ($this->creator_id == null) {
            return null;
        }

        if ($this->creator_id != null) {
            $creator = User::where("id", $this->creator_id)->first();
            if ($creator != null) {
                return $creator->name;
            }

            return null;
        }

    }

    public function getFormattedDocumentCustomerAttribute($value)
    {
        if ($this->customer_id == null) {
            return null;
        }

        if ($this->customer_id != null) {
            $customer = User::where("id", $this->customer_id)->first();
            if ($customer != null) {
                return $customer->name;
            }

            return null;
        }

    }

    public function getFormattedDocumentCashRegisterAttribute($value)
    {
        if ($this->cash_register_id == null) {
            return null;
        }

        if ($this->cash_register_id != null) {
            $cashregister = PosCashRegister::where("id", $this->cash_register_id)->first();
            if ($cashregister != null) {
                return $cashregister->name;
            }

            return null;
        }

    }

    public function getFormattedDocumentColorActionAttribute($value)
    {
        if ($this->action == "hold_create_completed") {
            return "V";
        }

        if ($this->action == "create") {
            return "V";
        }
        if ($this->action == "hold_item_create") {
            return "V";
        }


        if ($this->action == "hold_created") {
            return "V";
        }

        if ($this->action == "hold_create_item") {
            return "V";
        }
        if ($this->action == "hold_create_discount") {
            return "V";
        }

        if ($this->action == "hold_create_tax") {
            return "V";
        }

        if ($this->action == "hold_edit_tax_amount") {
            return "P";
        }

        if ($this->action == "hold_edit_item") {
            return "P";
        }

        if ($this->action == "hold_item_without_changes") {
            return "P";
        }

        if ($this->action == "hold_edited") {
            return "P";
        }

        if ($this->action == "hold_created_print") {
            return "V";
        }

        if ($this->action == "hold_create_note") {
            return "V";
        }

        if ($this->action == "hold_create_table") {
            return "V";
        }

        if ($this->action == "hold_edited_print") {
            return "P";
        }

        if ($this->action == "discount_without_change") {
            return "P";
        }

        if ($this->action == "hold_delete") {
            return "R";
        }

        if ($this->action == "hold_item_delete") {
            return "R";
        }

        if ($this->action == "hold_tax_create") {
            return "V";
        }

        if ($this->action == "hold_tax_delete") {
            return "R";
        }






    }

    public function getFormattedDocumentLangActionAttribute($value)
    {
        if ($this->action == "hold_create_completed") {
            return "coreposhistory.hold_create_completed";
        }


        if ($this->action == "create") {
            return "coreposhistory.hold_create_completed";
        }


        if ($this->action == "hold_created") {
            return "coreposhistory.hold_created";
        }

        if ($this->action == "hold_create_item") {
            return "coreposhistory.hold_create_item";
        }
        if ($this->action == "hold_create_discount") {
            return "coreposhistory.hold_create_discount";
        }

        if ($this->action == "hold_create_tax") {
            return "coreposhistory.hold_create_tax";
        }

        if ($this->action == "hold_edit_tax_amount") {
            return "coreposhistory.hold_edit_tax_amount";
        }

        if ($this->action == "hold_edit_item") {
            return "coreposhistory.hold_edit_item";
        }

        if ($this->action == "hold_item_without_changes") {
            return "coreposhistory.hold_item_without_changes";
        }

        if ($this->action == "hold_edited") {
            return "coreposhistory.hold_edited";
        }

        if ($this->action == "hold_created_print") {
            return "coreposhistory.hold_created_print";
        }

        if ($this->action == "hold_create_note") {
            return "coreposhistory.hold_create_note";
        }

        if ($this->action == "hold_create_table") {
            return "coreposhistory.hold_create_table";
        }

        if ($this->action == "hold_edited_print") {
            return "coreposhistory.hold_edited_print";
        }

        if ($this->action == "hold_item_create") {
            return "coreposhistory.hold_item_create";
        }

        if ($this->action == "hold_item_delete") {
            return "coreposhistory.hold_item_delete";
        }

        if ($this->action == "hold_tax_create") {
            return "coreposhistory.hold_tax_create";
        }

        if ($this->action == "hold_tax_delete") {
            return "coreposhistory.hold_tax_delete";
        }



    }

    public function scopeApplyFilters($query, array $filters)
    {

        if (array_key_exists('user_id', $filters)) {
            if ($filters["user_id"] != null) {

                $query->WhereUser($filters["user_id"]);

            }
        }

        if (array_key_exists('customer_id', $filters)) {
            if ($filters["customer_id"] != null) {

                $query->WhereCustomer($filters["customer_id"]);

            }
        }

        if (array_key_exists('item_id', $filters)) {

            if ($filters["item_id"] != null) {

                $query->WhereItem($filters["item_id"]);

            }

        }

        if (array_key_exists('document_number', $filters)) {
            if ($filters["document_number"] != null) {

                $query->WhereDocumentNumber($filters["document_number"]);

            }
        }

        if (array_key_exists('action', $filters)) {
            if ($filters["action"] != null) {

                $query->WhereAction($filters["action"]);

            }
        }

        if (array_key_exists('from_date', $filters) && array_key_exists('to_date', $filters)) {
            if ($filters["from_date"] != null && $filters["to_date"] != null) {

                $start = Carbon::createFromFormat('Y-m-d', $filters["from_date"]);
                $end = Carbon::createFromFormat('Y-m-d', $filters["to_date"]);
                $query->CorePosBetween($start, $end);

            }
        }

        $query->whereOrder($filters["orderByField"], $filters["orderBy"]);

    }

    public function scopeWhereUser($query, $user_id)
    {
        $query->Where('creator_id', $user_id);
    }

    public function scopeWhereCustomer($query, $customer_id)
    {
        $query->Where('customer_id', $customer_id);
    }

    public function scopeWhereItem($query, $item_id)
    {
        $query->Where('item_id', $item_id);
    }

    public function scopeWhereDocumentNumber($query, $document_number)
    {
        return $query->where('document_number', 'LIKE', '%'.$document_number.'%');
    }

    public function scopeWhereAction($query, $action)
    {
        return $query->where('action', 'LIKE', '%'.$action.'%');
    }

    public function scopeCorePosBetween($query, $start, $end)
    {
        return $query->whereBetween(
            'created_at',
            [$start->format('Y-m-d'), $end->format('Y-m-d')]
        );
    }

    public function scopeWhereOrder($query, $orderByField, $orderBy)
    {
        $query->orderBy($orderByField, $orderBy);
    }
}
